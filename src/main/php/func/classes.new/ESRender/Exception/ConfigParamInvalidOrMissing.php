<?php

/**
 * Exception shall be thrown when the configuration for an application is
 * missing or has wrong parameter.
 *
 *
 */
class ESRender_Exception_ConfigParamInvalidOrMissing
extends ESRender_Exception_Abstract
{

	/**
	 *
	 * @param string $AppId
	 */
	public function __construct($AppId, $ParamName, $Message = '', $Code = '', $Previous = null)
	{
		parent::__construct($Message, $Code, $Previous);

		$this
			->setAppId($AppId)
			->setParam($ParamName);
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
	 * @return ESRender_Exception_ConfigParamInvalidOrMissing
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

	/**
	 *
	 *
	 * @var string
	 */
	private $Param = '';

	/**
	 *
	 *
	 * @param string $Param
	 * @return ESRender_Exception_ConfigParamInvalidOrMissing
	 */
	protected function setParam($Param)
	{
		$this->Param = (string) $Param;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getParam()
	{
		return $this->Param;
	}

}
