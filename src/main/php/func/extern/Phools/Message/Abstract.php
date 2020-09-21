<?php

/**
 *
 *
 *
 */
abstract class Phools_Message_Abstract
implements Phools_Message_Interface
{

	/**
	 *
	 * @param string $String
	 * @param array $Params
	 */
	public function __construct($String, array $Params = array())
	{
		$this->setString($String);

		foreach( $Params as $Param )
		{
			$this->bindParam($Param);
		}
	}

	/**
	 * Free params
	 *
	 */
	public function __destruct()
	{
		$this->Params = null;
		$this->Context = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Message_Interface::localize()
	 */
	public function localize(
		Phools_Locale_Interface $Locale,
		Phools_Translate_Interface $Translate)
	{
		$String = $Translate->translate($this->getString(), $Locale);
		if ( ! $String )
		{
			$String = $this->getString();
		}

		foreach( $this->getParams() as $Param )
		{
			$String = str_replace($Param->getIdentifier(), $Param->format($Locale), $String);
		}

		return $String;
	}

	/**
	 *
	 * @var string
	 */
	private $String = '';

	/**
	 *
	 * @param string $String
	 *
	 * @return Phools_Message_Abstract
	 */
	protected function setString($String)
	{
		$this->String = (string) $String;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Message_Interface::getString()
	 */
	public function getString()
	{
		return $this->String;
	}

	/**
	 *
	 * @var array
	 */
	private $Params = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Message_Interface::addParam()
	 */
	public function bindParam(Phools_Message_Param_Interface $Param)
	{
		$this->Params[] = $Param;
	}

	/**
	 *
	 * @return array
	 */
	protected function getParams()
	{
		return $this->Params;
	}

}
