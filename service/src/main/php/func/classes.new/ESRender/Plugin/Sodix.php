<?php

require_once(dirname(__FILE__) . '/../../../../vendor/autoload.php');

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7;

class ESRender_Plugin_Sodix extends ESRender_Plugin_Abstract
{
    protected String $url;
    protected String $user;
    protected String $password;
    protected String $mimetypesPlayout;

    public function __construct(array $properties = [
        "url" => "",
        "user" => "",
        "password" => "",
        "mimetypesPlayout" => ""
    ]) {
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
            return;
        }
        if(!$isPayedMedia && $this->mimetypesPlayout) {
            if(!preg_match($this->mimetypesPlayout, $esObject->getMimeType())) {
                $logger->info('Sodix ' . $repId . ' mimetype is not supported: ' . $esObject->getMimeType() . ', allowed: ' . $this->mimetypesPlayout );
                return;
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
                "query" => "query paidMediaLinks {  paidMediaLinks(id: \"$repId\", role: \"$role\") {  links { href linkType } } }"
            ];
        }
        $response = $this->getGraphQL($token, $body);
        if (empty($response)) {
            return;
        }
        $this->handlePlayOut($repId, $response, $data, $isPayedMedia);
    }

    private function getToken(): String {
        $logger = $this->getLogger();
        $uri = substr($this->url, 0, -8) . '/auth/login';
        $client = GuzzleHelper::getClient();
        try {
            $result = $client->post($uri, [
                GuzzleHttp\RequestOptions::JSON =>["login" => $this->user, "password" => $this->password],
                'http_errors' => true
            ]);
        } catch (GuzzleHttp\Exception\ClientException | GuzzleHttp\Exception\TransferException $exception) {
            $logger->error(GuzzleHttp\Psr7\Message::toString($exception->getResponse()));
            return "";
        }

        return json_decode($result->getBody(), true)["access_token"] ?? "";
    }

    private function getGraphQL(String $token, array $body): array {
        $logger = $this->getLogger();
        $client = GuzzleHelper::getClient();
        try {
            $result = $client->post($this->url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                GuzzleHttp\RequestOptions::JSON => $body,
                'http_errors' => true
            ]);
        } catch (GuzzleHttp\Exception\ClientException | GuzzleHttp\Exception\TransferException $exception) {
            $logger->error(GuzzleHttp\Psr7\Message::toString($exception->getResponse()));
            return [];
        }

        return json_decode($result->getBody(), true);
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

    private function handlePlayOut(string $repId, array $response, &$data, bool $isPayedMedia): void {
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
        if(!$isPayedMedia && preg_match('/playout\.sodix\.de/', $playOutUrl)) {
            Config::set('urlEmbeddingIFrame', true);
            Config::set('urlEmbedding', '<iframe id="'.$unique.'" src="'. $playOutUrl . '" class="sodix-iframe '.$cssClass.'"></iframe>');
        }
    }
}
