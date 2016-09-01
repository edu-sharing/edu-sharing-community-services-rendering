<?php

/**
 * Exception shall be thrown when a given request-param is invalid.
 *
 *
 */
class ESRender_Exception_InvalidRequestParam
extends ESRender_Exception_Abstract
{

	/**
	 *
	 * @param string $ParamName
	 */
	public function __construct($ParamName, $Message = '', $Code = '', $Previous = null)
	{
		parent::__construct($Message, $Code, $Previous);

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
