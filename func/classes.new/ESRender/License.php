<?php
class ESRender_License {

	private $author = '';
	private $icon =  '';
	private $url = '';
	private $permalink = '';
	private $filename = '';

	public function __construct($esobject) {
        $author = str_replace('[#]', ', ', $esobject -> getContentNode() -> getNodeProperty('ccm:lifecyclecontributer_authorFN'));
        $authorFreeText = str_replace('[#]', ', ', $esobject -> getContentNode() -> getNodeProperty('ccm:author_freetext'));
        if($author && $authorFreeText) {
            $this -> author = $author . ' & ' . $authorFreeText;
        } else if($author) {
            $this -> author = $author;
        } else if($authorFreeText) {
            $this -> author = $authorFreeText;
        } else {
            $this -> author = $esobject -> getContentNode() -> getNodeProperty('ccm:metadatacontributer_creatorFN');
        }

		$this -> icon = $esobject -> getContentNode() -> getNode() -> license -> icon;
		$this -> url = $esobject -> getContentNode() -> getNode() -> license -> url;
		$this -> permalink = $esobject -> getContentNode() -> getNode() -> content -> url;
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