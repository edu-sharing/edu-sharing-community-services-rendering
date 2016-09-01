<?php

/**
 * As SMTP communicates by lines a typical connection must offer a mechanism
 * to do so.
 *
 *
 */
interface Phools_Net_Http_Connection_Interface
extends Phools_Net_Connection_Interface
{

	/**
	 *
	 * @param string $Data
	 *
	 * @return Phools_Net_Http_Connection_Interface
	 */
	public function writeLine($Data);

	/**
	 *
	 * @return string
	 */
	public function readLine();

}
