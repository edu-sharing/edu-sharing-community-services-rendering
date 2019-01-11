<?php
class ESRender_License {

	private $author = '';
	private $icon =  '';
	private $url = '';
	private $permalink = '';
	private $filename = '';

	public function __construct($esobject) {
        $author = str_replace('[#]', ', ', $esobject -> ContentNode -> getProperty('ccm:lifecyclecontributer_authorFN'));
        $authorFreeText = str_replace('[#]', ', ', $esobject -> ContentNode -> getProperty('ccm:author_freetext'));
        if($author && $authorFreeText) {
            $this -> author = $author . ' & ' . $authorFreeText;
        } else if($author) {
            $this -> author = $author;
        } else if($authorFreeText) {
            $this -> author = $authorFreeText;
        } else {
            $this -> author = $esobject -> ContentNode -> getProperty('ccm:metadatacontributer_creatorFN');
        }

		$this -> icon = $esobject -> ContentNode -> getProperty('{virtualproperty}licenseicon');
		$this -> url = $esobject -> ContentNode -> getProperty('{virtualproperty}licenseurl');
		$this -> permalink = $esobject -> ContentNode -> getProperty('{virtualproperty}permalink');
		$this -> filename = $esobject -> getTitle();
	}

	public function renderFooter(Phools_Template_Interface $Template, $url) {
		return $Template->render('/license/default', array(
				'license_author' => $this->author,
				'license_icon_url' => $this->icon,
				'license_url' => $this->url,
				'license_permalink' => $url,
				'license_filename' => $this -> filename
		));
	}
}