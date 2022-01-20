<?php

/**
 *
 *
 *
 */
interface Phools_Message_Param_Interface
{

	/**
	 *
	 * @return string
	 */
	public function getIdentifier();

	/**
	 *
	 * @param Phools_Locale_Interface $Locale
	 *
	 * @return string
	 */
	public function format(Phools_Locale_Interface $Locale = null);

}
