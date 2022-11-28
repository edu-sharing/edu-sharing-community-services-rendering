<?php
include_once ('../../modules/doc/mod_doc.php');
require_once ('../../vendor/autoload.php');

class mod_collection extends mod_doc {

    public function dynamic() {
        Config::set('showDownloadAdvice', false);
        return parent::dynamic();
    }

}