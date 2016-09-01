<?php

/**
 *
 *
 *
 */
class Phools_Net_Mime_Encoding_Base64
extends Phools_Net_Mime_Encoding_Abstract
{

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct('base64');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Encoding_Interface::encode()
	 */
	public function encode($String)
	{
		return base64_encode($String);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Encoding_Interface::decode()
	 */
	public function decode($String)
	{
		return base64_decode($String);
	}

}
