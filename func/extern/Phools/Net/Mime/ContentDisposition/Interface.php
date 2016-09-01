<?php

/**
 *
 *
 *
 */
interface Phools_Net_Mime_ContentDisposition_Interface
{

	/**
	 *
	 * @return string
	 */
	public function getType();

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 *
	 * @return Phools_Net_Mime_ContentDisposition_Interface
	 */
	public function setParam($Name, $Value);

	/**
	 *
	 * @param string $Name
	 *
	 * @return string
	 */
	public function getParam($Name);

}
