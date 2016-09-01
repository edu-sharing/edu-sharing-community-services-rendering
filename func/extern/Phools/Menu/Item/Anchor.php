<?php

/**
 *
 *
 *
 */
class Phools_Menu_Item_Anchor
extends Phools_Menu_Item_Abstract
{

	/**
	 *
	 *
	 * @param string $Name
	 * @param string $Url
	 */
	public function __construct($Name, $Url)
	{
		parent::__construct($Name);

		$this->setUrl($Url);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Menu_Item_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->Url = null;

		parent::__destruct();
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
	 * @return Phools_Menu_Item_Anchor
	 */
	public function setUrl($Url)
	{
		$this->Url = (string) $Url;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUrl()
	{
		return $this->Url;
	}

}
