<?php
class ESRender_License {

	private $author = '';
	private $icon =  '';
	private $url = '';
	private $permalink = '';
	private $filename = '';

	public function __construct($esobject) {
        $author = str_replace('[#]', ', ', $esobject -> getNodeProperty('ccm:lifecyclecontributer_authorFN'));
        if (is_array($author)) {
            $allAuthors = array_filter($author, fn($x) => !empty($x));
        } else {
            $allAuthors = [$author];
        }
        $orgs = str_replace('[#]', ', ', $esobject -> getNodeProperty('ccm:lifecyclecontributer_authorVCARD_ORG'));
        if (is_array($orgs)) {
            $allOrgs = array_filter($orgs, fn($x) => !empty($x));
        } else if(!empty($orgs)){
            $allOrgs = [$orgs];
        } else {
            $allOrgs = [];
        }
        $combinedAuthors = array_merge($allAuthors, $allOrgs);
        $authorFreeText = str_replace('[#]', ', ', $esobject -> getNodeProperty('ccm:author_freetext'));
        if($author && $authorFreeText) {
            $this -> author = implode(' & ', $combinedAuthors) . ' & ' . $authorFreeText;
        } else if($author) {
            $this -> author = $combinedAuthors;
        } else if($authorFreeText) {
            $this -> author = $authorFreeText;
        } else {
            $this -> author = $esobject -> getNodeProperty('ccm:metadatacontributer_creatorFN');
        }

		$this -> icon = $esobject -> getNode() -> license -> icon;
		$this -> url = $esobject -> getNode() -> license -> url;
		$this -> permalink = $esobject -> getNode() -> content -> url;
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
