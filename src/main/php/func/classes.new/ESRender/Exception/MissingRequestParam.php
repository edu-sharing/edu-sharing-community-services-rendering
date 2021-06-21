<?php

/**
 * Exception shall be thrown when a required request-param is missing.
 *
 *
 */
class ESRender_Exception_MissingRequestParam
extends ESRender_Exception_Abstract
{

	/**
	 *
	 * @param string $ParamName
	 */
	public function __construct($ParamName, $Message = '', $Code = '', $Previous = null)
	{
		$this->setParamName($ParamName);
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $ParamName = '';

	/**
	 *
	 *
	 * @param string $ParamName
	 * @return ESRender_Exception_MissingRequestParam
	 */
	protected function setParamName($ParamName)
	{
		$this->ParamName = (string) $ParamName;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getParamName()
	{
		return $this->ParamName;
	}

}
