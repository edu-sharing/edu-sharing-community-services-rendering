<?php

/**
 *
 *
 *
 */
class Phools_Escaping_Html_Entities
extends Phools_Escaping_Html_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Escaping_Interface::escape()
	 */
	public function escape($String)
	{
		return htmlentities(
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
		return html_entity_decode(
			$String,
			$this->getQuotingStyle(),
			$this->getCharset());
	}

}
