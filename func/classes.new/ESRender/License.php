<?php
class ESRender_License {

	private $author = '';
	private $icon =  '';
	private $url = '';
	private $permalink = '';
	private $filename = '';

	public function __construct($esobject) {
		$this -> author = ($esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}lifecyclecontributer_authorFN')) ? $esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}lifecyclecontributer_authorFN') : $esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}metadatacontributer_creatorFN');
        if($esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}author_freetext'))
		    $this -> author .= ' & ' . $esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}author_freetext');
		$this -> icon = $esobject -> AlfrescoNode -> getProperty('{virtualproperty}licenseicon');
		$this -> url = $esobject -> AlfrescoNode -> getProperty('{virtualproperty}licenseurl');
		$this -> permalink = $esobject -> AlfrescoNode -> getProperty('{virtualproperty}permalink');
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