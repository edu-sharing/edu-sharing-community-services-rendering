<?php

/**
 *
 *
 */
interface Phools_Net_Mime_Type_Interface
{

	/**
	 *
	 * @return string
	 */
	public function getMimeType();

	/**
	 * Convinience method to extract the MIME-type's primary type.
	 *
	 * @return string
	 */
	public function getPrimaryType();

	/**
	 * Convinience method to extract the MIME-type's subtype.
	 *
	 * @return string
	 */
	public function getSubtype();

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 *
	 */
	public function setParam($Name, $Value = '');

	/**
	 *
	 * @param string $Name
	 *
	 * @return string
	 */
	public function getParam($Name);

}
