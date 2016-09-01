<?php

/**
 *
 *
 *
 */
abstract class Phools_Locale_Abstract
implements Phools_Locale_Interface
{

	/**
	 *
	 * @param string $LanguageCodeTwoLetters
	 * @param string $CountryCodeTwoLetters
	 * @param string $DecimalSeparator
	 * @param string $ThousandsSeparator
	 */
	public function __construct(
		$LanguageCode,
		$CountryCode,
		$DecimalSeparator = '.',
		$ThousandsSeparator = '')
	{
		$this
			->setLanguageTwoLetters($LanguageCode)
			->setCountryTwoLetters($CountryCode)
			->setDecimalSeparator($DecimalSeparator)
			->setThousandsSeparator($ThousandsSeparator);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::formatNumber()
	 */
	public function formatNumber($Value, $Decimals)
	{
		$FormattedNumber = number_format(
			$Value,
			$Decimals,
			$this->getDecimalSeparator(),
			$this->getThousandsSeparator());

		return $FormattedNumber;
	}

	/**
	 *
	 * @var string
	 */
	protected $CountryName = '';

	/**
	 *
	 * @param string $Name
	 */
	public function setCountryName($Name)
	{
		$this->CountryName = (string) $Name;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::getCountryName()
	 */
	public function getCountryName()
	{
		return $this->CountryName;
	}

	/**
	 *
	 * @var string
	 */
	protected $CountryTwoLetters = '';

	/**
	 *
	 * @param string $Code
	 */
	public function setCountryTwoLetters($Code)
	{
		$this->CountryTwoLetters = (string) $Code;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::getCountryTwoLetters()
	 */
	public function getCountryTwoLetters()
	{
		return $this->CountryTwoLetters;
	}

	/**
	 *
	 * @var string
	 */
	protected $CountryThreeLetters = '';

	/**
	 *
	 * @param string $Code
	 */
	public function setCountryThreeLetters($Code)
	{
		$this->CountryThreeLetters = (string) $Code;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::getCountryThreeLetters()
	 */
	public function getCountryThreeLetters()
	{
		return $this->CountryThreeLetters;
	}

	/**
	 *
	 * @var string
	 */
	protected $LanguageName = '';

	/**
	 *
	 * @param string $Name
	 */
	public function setLanguageName($Name)
	{
		$this->LanguageName = (string) $Name;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::getLanguageName()
	 */
	public function getLanguageName()
	{
		return $this->LanguageName;
	}

	/**
	 *
	 * @var string
	 */
	protected $LanguageTwoLetters = '';

	/**
	 *
	 * @param string $Code
	 */
	public function setLanguageTwoLetters($Code)
	{
		$this->LanguageTwoLetters = (string) $Code;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::getLanguageTwoLetters()
	 */
	public function getLanguageTwoLetters()
	{
		return $this->LanguageTwoLetters;
	}

	/**
	 *
	 * @var string
	 */
	protected $LanguageThreeLetters = '';

	/**
	 *
	 * @param string $Code
	 */
	public function setLanguageThreeLetters($Code)
	{
		$this->LanguageThreeLetters = (string) $Code;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::getLanguageThreeLetters()
	 */
	public function getLanguageThreeLetters()
	{
		return $this->LanguageThreeLetters;
	}

	/**
	 *
	 * @var string
	 */
	protected $DecimalSeparator = '.';

	/**
	 *
	 * @param string $Separator
	 */
	public function setDecimalSeparator($Separator)
	{
		$this->DecimalSeparator = (string) $Separator;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::getDecimalSeparator()
	 */
	public function getDecimalSeparator()
	{
		return $this->DecimalSeparator;
	}

	/**
	 *
	 * @var string
	 */
	protected $ThousandsSeparator = '';

	/**
	 *
	 * @param string $Separator
	 */
	public function setThousandsSeparator($Separator)
	{
		$this->ThousandsSeparator = (string) $Separator;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Locale_Interface::getThousandsSeparator()
	 */
	public function getThousandsSeparator()
	{
		return $this->ThousandsSeparator;
	}

}
