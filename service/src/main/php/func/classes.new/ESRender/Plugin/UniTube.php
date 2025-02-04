<?php

require_once(dirname(__FILE__) . '/../../../../vendor/autoload.php');

class EsRender_Plugin_UniTube extends ESRender_Plugin_Abstract
{
    protected string $needle;
    protected string $portalUrl;

    public function __construct(array $properties = ["needle" => "", "portalUrl" => ""]) {
        parent::__construct($properties);
    }

    public function postRetrieveObjectProperties(&$data): void {
        $logger   = $this->getLogger();
        $esObject = new ESObject($data);
        $url      = $esObject->getNodeProperty('ccm:wwwurl');
        if (empty($url)) {
            return;
        }
        $url = html_entity_decode($url);
        if (!str_contains($url, $this->needle)) {
            return;
        }
        $urlToUse = $this->getUrlToUse($url);
        $logger->info("Detected UniTube video. Url for embedding: " . $urlToUse);
        $data->node->properties->{'ccm:wwwurl'} = $urlToUse;
        $this->setEmbedding($urlToUse);
    }

    private function getUrlToUse(string $url): string {
        if (!str_contains($url, 'portal')) {
            return $url;
        }
        parse_str(parse_url($url)['query'], $urlParams);
        $vidId = $urlParams['id'];
        return $this->portalUrl . '?id=' . $vidId;
    }

    private function setEmbedding(string $urlToUse): void {
        $embedding = '<div class="videoWrapperOuter customEmbedding">
                        <div class="videoWrapperInner">
                        <iframe id="' . uniqid() . '" src="' . $urlToUse . '" src="" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="embedded_video"></iframe>
                    </div>
                    {{VIDEO_FOOTER_PLACEHOLDER}}
                </div>';
        Config::set('urlEmbedding', $embedding);
    }
}
