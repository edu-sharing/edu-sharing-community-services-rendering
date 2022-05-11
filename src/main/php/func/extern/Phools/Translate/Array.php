<?php

/**
 *
 *
 */
class Phools_Translate_Array
extends Phools_Translate_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Translate_Interface::translate()
	 */
	public function translate($Identifier, Phools_Locale_Interface $Locale)
	{
		$LanguageCode = $Locale->getLanguageTwoLetters();
		if ( empty($LanguageCode) )
		{
			error_log('No language-code found in locale.', E_USER_NOTICE);
			return null;
		}

		$LanguageCode = strtolower($LanguageCode);

		if ( empty($this->Translations[$LanguageCode]) )
		{
			error_log('No translations found for language "'.$LanguageCode.'"', E_USER_NOTICE);
			return null;
		}

		if ( empty($this->Translations[$LanguageCode][$Identifier]) )
		{
			error_log('No translation found for "'.$Identifier.'" in language "'.$LanguageCode.'"', E_USER_NOTICE);
			return null;
		}

		$Message = $this->Translations[$LanguageCode][$Identifier];

		return $Message;
	}

	/**
	 *
	 * @var array
	 */
	private $Translations = array();

	/**
	 *
	 * @param string $LanguageCode
	 * @param string $Identifier
	 * @param string $Translation
	 */
	public function addTranslation($LanguageCode, $Identifier, $Translation)
	{
		$LanguageCode = strtolower($LanguageCode);

		if ( empty($this->Translations[$LanguageCode]) )
		{
			$this->Translations[$LanguageCode] = array();
		}

		$this->Translations[$LanguageCode][$Identifier] = $Translation;

		return $this;
	}

}
