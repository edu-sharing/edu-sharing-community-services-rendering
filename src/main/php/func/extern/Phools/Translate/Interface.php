<?php

/**
 *
 *
 *
 */
interface Phools_Translate_Interface
{

	/**
	 * Translate given identifier.
	 *
	 * @param string $Message
	 * @param Phools_Locale_Interface $Locale
	 *
	 * @return string | null
	 */
	public function translate($Message, Phools_Locale_Interface $Locale);

}
