<?php

require_once(dirname(__FILE__) . '/../../../../vendor/autoload.php');

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7;

class ESRender_Plugin_Sodix extends ESRender_Plugin_Abstract
{
    const Timeout = 5;
    protected String $url;
    protected String $user;
    protected String $password;
    protected String $mimetypesPlayout;
    protected String $allowExternalFrameSrc;
    protected String $sodixRegion;

    private \Predis\Client $redisClient;
    private const TOKEN_CACHE_KEY = 'sodix:token';

    public function __construct(array $properties = [
        "url" => "",
        "user" => "",
        "password" => "",
        "mimetypesPlayout" => "",
        "sodixRegion" => "",
        "allowExternalFrameSrc" => false
    ]) {
        $this->redisClient = RedisClient::getInstance()->getClient();
        parent::__construct($properties);
    }

    public function postRetrieveObjectProperties(&$data): void {
        $esObject = new ESObject($data);
        $logger = $this->getLogger();
        if ($esObject->getNodeProperty('ccm:replicationsource') !== 'SODIX') {
            return;
        }
        if (empty($esObject->getNodeProperty('ccm:replicationsourceid'))) {
            $logger->error("No SODIX ID found for");
        }
        $isPayedMedia = $esObject->getNodeProperty('ccm:editorial_state') === 'restricted_mz';

        $logger->info("Started communication with SODIX API for node" . ($isPayedMedia ? ' (payed media)' : ''));
        $repId = $esObject->getNodeProperty('ccm:replicationsourceid');
        $token = $this->getToken();
        if (empty($token)) {
            $logger->error("Token could not be retrieved, aborting");
            $this->displayError(
                'sodix_fetch_error',
                [':identifier' => $repId, ':error' => 'Error while trying to reach FWU Sodix API']
            );
            return;
        }
        if(!$isPayedMedia) {
            $downloadUrl = $this->fetchPublicDownloadUrl($data, $repId, $token);
            if($downloadUrl) {
                Config::set('downloadUrl', $downloadUrl);
            }
            if($this->mimetypesPlayout) {
                if(!preg_match($this->mimetypesPlayout, $esObject->getMimeType())) {
                    $logger->info('Sodix ' . $repId . ' mimetype is not supported: ' . $esObject->getMimeType() . ', allowed: ' . $this->mimetypesPlayout );
                    return;
                }
            }
        }
        $logger->info("Successfully retrieved token.");
        if (!$isPayedMedia) {
            $body = [
                "operationName" => "getPlayoutWindow",
                "query" => "query getPlayoutWindow {  getPlayoutWindow(mediaId: \"$repId\", autoplay: false) {  playoutUrl } }"
            ];
        } else {
            $role = 'LEARNER';
            $esObject = new ESObject($data);
            if($esObject->getUser()->primaryAffiliation === 'teacher') {
                $role = 'TEACHER';
            }
            $body = [
                "operationName" => "paidMediaLinks",
                "query" => "query paidMediaLinks {  paidMediaLinks(id: \"$repId\", role: $role, region: \"$this->sodixRegion\") {  links { href linkType } } }"
            ];
        }
        $response = $this->getGraphQL($token, $body);
        if (empty($response)) {
            return;
        }
        $this->handlePlayOut($token, $repId, $response, $data, $isPayedMedia);
    }
    private function fetchPublicDownloadUrl(&$data, $repId, $token): ?String {
        // try to fetch temporary download url
        if(isset($data->node->properties->{'ccm:external_download_allowed'}) && $data->node->properties->{'ccm:external_download_allowed'}[0] === 'true') {
            try {
                $body = [
                    "operationName" => "metadataByIdentifier",
                    "query" => "query metadataByIdentifier {  metadataByIdentifier(identifier: \"$repId\") {  media { downloadUrl } } }"
                ];
                $response = $this->getGraphQL($token, $body);
                if (!empty($response)) {
                    $url = $response['data']['metadataByIdentifier']['media']['downloadUrl'];
                    if($url) {
                        $this->getLogger()->info('Sodix ' . $repId . ' download url response: ' . $url);
                    } else {
                        $this->getLogger()->info('Sodix ' . $repId . ' no download url response');
                    }
                    return $url;
                }
            }catch(Exception $e) {
                $this->getLogger()->warn('Can not fetch downloadUrl', $e);
            }
        }
        return null;
    }

    private function getToken(): string {
        try {
            $cached = $this->redisClient->get(self::TOKEN_CACHE_KEY);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        } catch (\Throwable $e) {
            $this->getLogger()->warn('Redis unavailable while reading token cache; continuing without cache.', $e);
        }

        $token = $this->fetchNewToken();
        if ($token === '') {
            return '';
        }

        try {
            $this->redisClient->set(self::TOKEN_CACHE_KEY, $token);
        } catch (\Throwable $e) {
            $this->getLogger()->warn('Redis unavailable while writing token cache; continuing without cache.', $e);
        }

        return $token;
    }

    private function invalidateTokenCache(): void {
        try {
            $this->redisClient->del([self::TOKEN_CACHE_KEY]);
        } catch (\Throwable $e) {
            $this->getLogger()->warn('Redis unavailable while invalidating token cache.', $e);
        }
    }

    private function fetchNewToken(): String {
        $logger = $this->getLogger();
        $uri = substr($this->url, 0, -8) . '/auth/login';
        $client = GuzzleHelper::getClient();
        try {
            $result = $client->post($uri, [
                'timeout'  => self::Timeout,
                GuzzleHttp\RequestOptions::JSON =>["login" => $this->user, "password" => $this->password],
                'http_errors' => true
            ]);
        } catch (GuzzleHttp\Exception\ConnectException $exception) {
            $logger->error($exception->getMessage());
            return "";
        } catch (GuzzleHttp\Exception\ClientException | GuzzleHttp\Exception\TransferException $exception) {
            $logger->error(GuzzleHttp\Psr7\Message::toString($exception->getResponse()));
            return "";
        }

        return json_decode($result->getBody(), true)["access_token"] ?? "";
    }

    private function requestGraphQl(string $token, array $body): ?array {
        $logger = $this->getLogger();
        $client = GuzzleHelper::getClient();
        try {
            $result = $client->post($this->url, [
                'timeout'  => self::Timeout,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                GuzzleHttp\RequestOptions::JSON => $body,
                'http_errors' => true
            ]);
        } catch (GuzzleHttp\Exception\ConnectException $exception) {
            $logger->error($exception->getMessage());
            return [];
        } catch ( GuzzleHttp\Exception\ClientException $clientException) {
            $status = $clientException->getResponse()?->getStatusCode();
            if ($status === 401 || $status === 403) {
                // Token invalid/expired -> tell caller to refresh and retry once
                return null;
            }
            $logger->error(GuzzleHttp\Psr7\Message::toString($clientException->getResponse()));
            return [];
        }  catch (GuzzleHttp\Exception\TransferException $exception) {
            $logger->error(GuzzleHttp\Psr7\Message::toString($exception->getResponse()));
            return [];
        }

        return json_decode($result->getBody(), true);
    }

    private function getGraphQL(String $token, array $body): array {
        $logger = $this->getLogger();

        $response = $this->requestGraphQl($token, $body);
        if ($response !== null) {
            return $response;
        }
        $logger->info('SODIX token seems stale; fetching a new token and retrying once.');
        $this->invalidateTokenCache();

        $freshToken = $this->getToken();
        if ($freshToken === '') {
            return [];
        }

        $response = $this->requestGraphQL($freshToken, $body);
        return $response ?? [];
    }

    public function displayError($message, array $data = []) {
        global $Locale, $Translate;
        $data = array_map(
            function($key, $value) {
                return new Phools_Message_Param_String($key, $value);
            },
            array_keys($data),
            $data
        );
        $Message = new Phools_Message_Default('sodixPluginError', $data);
        echo '<div class="plugin-error-message">' . $Message -> localize($Locale, $Translate) . '</div>';
    }

    private function handlePlayOut(string $token, string $repId, array $response, &$data, bool $isPayedMedia): void {
        $logger = $this->getLogger();
        if ($response["errors"][0]["extensions"]["classification"] ?? "" === "DataFetchingException") {
            $this->displayError(
                'sodix_fetch_error',
                [':identifier' => $repId, ':error' => ($response["errors"][0]["message"] ?: print_r($response["errors"][0], true))]
            );
            $logger->error("SODIX content contains errors. Url could not be found or retrieved.");
            return;
        }
        if ($isPayedMedia) {
            $playOutLinkEntry = array_filter($response['data']['paidMediaLinks']['links'] ?? [], fn($link) => $link['linkType'] === 'direct');
            $playOutUrl = reset($playOutLinkEntry)['href'] ?? '';
            Config::set('RemoteObjectType', 'generic');
            $downloadLinkEntry = array_filter($response['data']['paidMediaLinks']['links'] ?? [], fn($link) => $link['linkType'] === 'download');
            if (!empty($downloadLinkEntry)) {
                Config::set('downloadUrl', reset($downloadLinkEntry)['href'] ?? '');
            }
        } else {
            $playOutUrl = $response["data"]["getPlayoutWindow"]["playoutUrl"] ?? "";
        }
        if (empty($playOutUrl)) {
            $logger->error("SODIX response does not contain expected url: " . json_encode($response));
        }
        $logger->info("SODIX url successfully retrieved.");
        $data->node->properties->{'ccm:wwwurl'} = $playOutUrl;
        $unique = uniqid();
        $cssClass="sodix-iframe-video";
        if($data->node->mediatype === 'file-audio') {
            $cssClass="sodix-iframe-audio";
        }
        if(!$isPayedMedia) {
            // try to fetch temporary download url
            if(isset($data->node->properties->{'ccm:external_download_allowed'}) && $data->node->properties->{'ccm:external_download_allowed'}[0] === 'true') {
                try {
                    $body = [
                        "operationName" => "metadataByIdentifier",
                        "query" => "query metadataByIdentifier {  metadataByIdentifier(identifier: \"$repId\") {  media { downloadUrl } } }"
                    ];
                    $response = $this->getGraphQL($token, $body);
                    if (!empty($response)) {
                        $url = $response['data']['metadataByIdentifier']['media']['downloadUrl'];
                        if($url) {
                            Config::set('downloadUrl', $url);
                            $logger->info('Sodix ' . $repId . ' download url response: ' . $url);
                        } else {
                            $logger->info('Sodix ' . $repId . ' no download url response');
                        }
                    }
                }catch(Exception $e) {
                    $logger->warn('Can not fetch downloadUrl', $e);
                }
            }
            if(preg_match('/playout\.sodix\.de/', $playOutUrl) || $this->allowExternalFrameSrc == true) {
                Config::set('urlEmbeddingIFrame', true);
                Config::set('urlEmbedding', '<iframe id="'.$unique.'" src="'. $playOutUrl . '" class="sodix-iframe '.$cssClass.'"></iframe>');
            } else {
                if(isset($data->node->properties->{'cclom:location'})) {
                    // todo: check for mp4 and mp3 ending and video/audio
                    $esObject = new ESObject($data);
                    $mime = explode('/', strtolower($esObject->getMimeType()));
                    $end = strtolower(substr($data->node->properties->{'cclom:location'}[0], -3));
                    if ($mime[0] === 'video' || $mime[0] === 'audio' && $end === 'mp4' || $end === 'mp3') {
                        $data->node->properties->{'ccm:wwwurl'} = $data->node->properties->{'cclom:location'};
                    } else {
                        Config::set('RemoteObjectType', 'generic');
                    }
                } else {
                    Config::set('RemoteObjectType', 'generic');
                }
            }
        }
    }
}
