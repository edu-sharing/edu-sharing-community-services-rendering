<?php

/**
 *
 *
 *
 */
interface Phools_Locale_Interface
{

	/**
	 *
	 * @param float $Value
	 * @param int $Decimals
	 *
	 * @return string
	 */
	public function formatNumber($Value, $Decimals);

	/**
	 * Return the english country-name.
	 *
	 * @return string
	 */
	public function getCountryName();

	/**
	 * Return the 2-letter ISO-3166 code for this country.
	 *
	 * @return string
	 */
	public function getCountryTwoLetters();

	/**
	 * Return the 3-letter ISO-3166 code for this country.
	 *
	 * @return string
	 */
	public function getCountryThreeLetters();

	/**
	 * Return the english name of current language.
	 *
	 * @return string
	 */
	public function getLanguageName();

	/**
	 * Return the 2-letter ISO-639 code for current language.
	 *
	 * @return string
	 */
	public function getLanguageTwoLetters();

	/**
	 * Return the 3-letter ISO-639 code for current language.
	 *
	 * @return string
	 */
	public function getLanguageThreeLetters();

	/**
	 *
	 * @return string
	 */
	public function getDecimalSeparator();

	/**
	 *
	 * @return string
	 */
	public function getThousandsSeparator();

}
