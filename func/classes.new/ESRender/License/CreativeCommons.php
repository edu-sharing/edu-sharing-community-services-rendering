<?php

/**
 *
 *
 */
class ESRender_License_CreativeCommons
extends ESRender_License_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param string $Author
	 * @param string $Url
	 * @param string $IconUrl
	 */
	public function __construct($Name, $Author, $IconUrl, $Url, $permalink, $fileName)
	{
		parent::__construct($Name, $Author, $IconUrl, $permalink, $fileName);

		$this->setUrl($Url);
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_License_Interface::renderFooter()
	 */
	public function renderFooter(Phools_Template_Interface $Template)
	{
		return $Template->render('/license/creative_commons/footer', array(
		    'license_name' => $this -> getName(),
			'license_author' => $this->getAuthor(),
			'license_icon_url' => $this->getIconUrl(),
			'license_url' => $this->getUrl(),
			'license_permalink' => $this -> getPermalink(),
			'license_filename' => $this -> getFileName()
		));
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Url = '';

	/**
	 *
	 *
	 * @param string $Url
	 * @return ESRender_License_CreativeCommons
	 */
	public function setUrl($Url)
	{
		assert( is_string($Url) );
		assert( 0 < strlen($Url) );

		$this->Url = $Url;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->Url;
	}

}
