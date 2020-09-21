<?php

/**
 *
 *
 *
 */
interface Phools_Message_Interface
{

	/**
	 *
	 * @return string
	 */
	public function getString();

	/**
	 *
	 * @param Phools_Locale_Interface $Locale
	 * @param Phools_Translate_Interface $Translate
	 */
	public function localize(
		Phools_Locale_Interface $Locale,
		Phools_Translate_Interface $Translate);

	/**
	 * Associate param with this message.
	 *
	 * @param Phools_Message_Param_Interface $Param
	 *
	 * @return Phools_Message_Interface
	 */
	public function bindParam(Phools_Message_Param_Interface $Param);

}
