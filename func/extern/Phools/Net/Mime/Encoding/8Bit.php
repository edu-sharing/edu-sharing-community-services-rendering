<?php

/**
 *
 *
 *
 */
class Phools_Net_Mime_Encoding_8Bit
extends Phools_Net_Mime_Encoding_Abstract
{

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct('8bit');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Encoding_Interface::encode()
	 */
	public function encode($String)
	{
		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Encoding_Interface::encode()
	 */
	public function decode($String)
	{
		return $String;
	}

}
