<?php

require_once(dirname(__FILE__) . '/../../../../vendor/autoload.php');

class EsRender_Plugin_CustomEduHtml extends ESRender_Plugin_Abstract
{
    protected String $indexFilesJson;
    private Array $indexFiles = [];

    public function __construct(array $properties = ["indexFiles" => ""]) {
        parent::__construct($properties);
        try {
            $this->indexFiles = json_decode(json: $this->indexFilesJson, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->getLogger()->error("Invalid JSON array provided to indexFilesJson: " . $exception->getMessage());
        }
    }

    public function postRetrieveObjectProperties(&$data): void {
        Config::set('eduHtmlIndexFiles', $this->indexFiles);
    }
}
