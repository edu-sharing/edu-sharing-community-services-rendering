<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Writer_Echo
extends Phools_Net_Smtp_Writer_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Writer_Interface::flush()
	 */
	public function flush()
	{
	}

	public function append($String)
	{
		echo $String;
	}

}
