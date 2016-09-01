<?php

/**
 * Base-class for validators which work by evaluating a regular expression.
 * As no error-messages could be known beforehand you are expected to create
 * some in extending classes.
 *
 */
abstract class Phools_Validator_Regex_Abstract
extends Phools_Validator_Abstract
{

	/**
	 *
	 * @param string $RegEx
	 * @param string $Delimiter
	 * @param string $CustomErrorMessage
	 */
	public function __construct($RegEx, $Delimiter = '/', $CustomErrorMessage)
	{
		$this->setRegEx($RegEx)
			->setDelimiter($Delimiter)
			->setCustomErrorMessage($CustomErrorMessage);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Interface::validate()
	 */
	public function validate($Value)
	{
		$RegEx = $this->getRegEx();
		$Result = preg_match($RegEx, $Value);

		if ( false === $Result )
		{
			throw new Exception('Error evaluating regular expression.');
		}

		if ( 0 < $Result )
		{
			$this->clearErrorMessages();

			return true;
		}

		$this->addErrorMessage($this->getCustomErrorMessage());

		return false;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $RegEx = '';

	/**
	 *
	 *
	 * @param string $RegEx
	 * @return
	 */
	public function setRegEx($RegEx)
	{
		$this->RegEx = (string) $RegEx;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getRegEx()
	{
		return $this->RegEx;
	}

	/**
	 *
	 *
	 * @var
	 */
	protected $Delimiter = '';

	/**
	 *
	 *
	 * @param  $Delimiter
	 * @return class
	 */
	public function setDelimiter($Delimiter)
	{
		$this->Delimiter = (string) $Delimiter;
		return $this;
	}

	/**
	 *
	 * @return
	 */
	protected function getDelimiter()
	{
		return $this->Delimiter;
	}

	/**
	 *
	 *
	 * @var
	 */
	protected $CustomErrorMessage = '';

	/**
	 *
	 *
	 * @param  $CustomErrorMessage
	 * @return class
	 */
	public function setCustomErrorMessage($CustomErrorMessage)
	{
		$this->CustomErrorMessage = (string) $CustomErrorMessage;
		return $this;
	}

	/**
	 *
	 * @return
	 */
	protected function getCustomErrorMessage()
	{
		return $this->CustomErrorMessage;
	}

}
