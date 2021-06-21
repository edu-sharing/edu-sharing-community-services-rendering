<?php

/**
 * Exception shall be thrown when the configuration for an application could
 * not be loaded.
 *
 *
 */
class ESRender_Exception_AppConfigNotLoaded
extends ESRender_Exception_Abstract
{

	/**
	 *
	 * @param string $AppId
	 */
	public function __construct($AppId, $Message = '', $Code = '', $Previous = null)
	{
		parent::__construct($Message, $Code, $Previous);

		$this->setAppId($AppId);
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $AppId = '';

	/**
	 *
	 *
	 * @param string $AppId
	 * @return ESRender_Exception_MissingRequestParam
	 */
	protected function setAppId($AppId)
	{
		$this->AppId = (string) $AppId;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getAppId()
	{
		return $this->AppId;
	}

}
