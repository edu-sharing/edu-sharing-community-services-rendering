<?php

/**
 *
 *
 *
 */
class Phools_Net_Mime_Encoding_Binary
extends Phools_Net_Mime_Encoding_Abstract
{

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct('binary');
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
