<?php
include_once ('../../modules/doc/mod_doc.php');

class mod_collection extends mod_doc {

    public function dynamic() {
        Config::set('showDownloadAdvice', false);
        return parent::dynamic();
    }

}