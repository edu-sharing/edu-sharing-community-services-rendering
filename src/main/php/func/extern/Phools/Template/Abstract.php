<?php

/**
 * Base-class to derive renderer's from.
 *
 */
abstract class Phools_Template_Abstract
implements Phools_Template_Interface
{

	/**
	 *
	 *
	 * @var string
	 */
	private $Theme = '';

	/**
	 *
	 *
	 * @param string $Theme
	 * @return Phools_Template_Abstract
	 */
	public function setTheme($Name = '')
	{
		assert( is_string($Name) );
		assert( 0 < strlen($Name) );

		$this->Theme = $Name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getTheme()
	{
		return $this->Theme;
	}

	/**
	 *
	 *
	 * @var Phools_Translate_Interface
	 */
	protected $Translate = null;

	/**
	 *
	 *
	 * @param Phools_Translate_Interface $Translate
	 * @return Phools_Template_Abstract
	 */
	public function setTranslate(Phools_Translate_Interface $Translate)
	{
		$this->Translate = $Translate;
		return $this;
	}

	/**
	 *
	 * @return Phools_Translate_Interface
	 */
	protected function getTranslate()
	{
		return $this->Translate;
	}

	/**
	 *
	 *
	 * @var Phools_Locale_Interface
	 */
	protected $Locale = null;

	/**
	 *
	 *
	 * @param Phools_Locale_Interface $Locale
	 * @return Phools_Template_Abstract
	 */
	public function setLocale(Phools_Locale_Interface $Locale)
	{
		$this->Locale = $Locale;
		return $this;
	}

	/**
	 *
	 * @return Phools_Locale_Interface
	 */
	protected function getLocale()
	{
		return $this->Locale;
	}

	/**
	 * Translate message.
	 *
	 * @param string $Message
	 *
	 * @return string
	 */
	protected function translate($Message)
	{
		$Locale = $this->getLocale();
		if ( ! $Locale )
		{
			return $Message;
		}

		$Translate = $this->getTranslate();
		if ( ! $Translate )
		{
			return $Message;
		}

		$Message = $Translate->translate($Message, $Locale);

		return $Message;
	}


}
