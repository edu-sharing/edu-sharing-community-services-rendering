<?php

/**
 *
 *
 *
 */
class Phools_Escaping_Html_Specialchars
extends Phools_Escaping_Html_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Escaping_Interface::escape()
	 */
	public function escape($String)
	{
		return htmlspecialchars(
			$String,
			$this->getQuotingStyle(),
			$this->getCharset());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Escaping_Interface::unescape()
	 */
	public function unescape($String)
	{
		return htmlspecialchars_decode(
			$String,
			$this->getQuotingStyle());
	}

}
