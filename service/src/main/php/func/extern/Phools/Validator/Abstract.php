<?php

/**
 *
 *
 *
 */
abstract class Phools_Validator_Abstract
implements Phools_Validator_Interface
{

	/**
	 *
	 */
	public function __destruct()
	{
		$this->FirstChild = null;
		$this->LastChild = null;

		$this->NextSibling = null;
		$this->PreviousSibling = null;

		$this->ParentValidator = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Interface::validate()
	 */
	public function validate($Value)
	{
		$Result = true;

		$Child = $this->getFirstChild();
		while( $Child )
		{
			$Result = $Result && $Child->validate($Value);

			if ( ! $Result )
			{
				if ( $this->breakChainOnFailure() )
				{
					return $Result;
				}
			}

			$Child = $Child->getNextSibling();
		}

		return $Result;
	}

	/**
	 * Keeps the last validation-ErrorMessage.
	 *
	 * @var array
	 */
	private $ErrorMessages = array();

	/**
	 *
	 *
	 * @param  $ErrorMessage
	 * @return Phools_Validator_Abstract
	 */
	protected function addErrorMessage($ErrorMessage)
	{
		$this->ErrorMessages[] = (string) $ErrorMessage;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Interface::getErrorMessages()
	 */
	public function getErrorMessages()
	{
		$ErrorMessages = $this->ErrorMessages;

		$Child = $this->getFirstChild();
		while( $Child )
		{
			$ChildErrorMessages = $Child->getErrorMessages();
			$ErrorMessages = array_merge($ErrorMessages, $ChildErrorMessages);

			$Child = $Child->getNextSibling();
		}

		return $ErrorMessages;
	}

	/**
	 *
	 * @return Phools_Validator_Abstract
	 */
	protected function clearErrorMessages()
	{
		$this->ErrorMessages = array();
	}

	/**
	 *
	 *
	 * @var
	 */
	protected $HasValidated = false;

	/**
	 *
	 *
	 * @param  $Validated
	 * @return Phools_Validator_Abstract
	 */
	protected function setHasValidated($HasValidated)
	{
		$this->HasValidated = (bool) $HasValidated;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Interface::hasValidated()
	 */
	public function hasValidated()
	{
		return $this->HasValidated;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Interface::reset()
	 */
	public function reset()
	{
		$this->ErrorMessages = array();
		$this->setHasValidated(false);

		$Child = $this->getFirstChild();
		while( $Child )
		{
			$Child->reset();
			$Child = $Child->getNextSibling();
		}

		return $this;
	}

	/**
	 *
	 *
	 * (non-PHPdoc)
	 * @see Phools_Validator_Interface::prependValidator()
	 */
	public function prependValidator(Phools_Validator_Interface $Validator)
	{
		$FirstChild = $this->getFirstChild();
		if ( ! $FirstChild )
		{
			$this->setFirstChild($Validator);
			$this->setFirstChild($Validator);
		}
		else {
			$Validator->setNextSibling($FirstChild);
			$FirstChild->setPreviousSibling($Validator);

			$this->setFirstChild($Validator);
		}

		$Validator->setParentValidator($this);

		return $this;
	}

	/**
	 *
	 *
	 * (non-PHPdoc)
	 * @see Phools_Validator_Interface::appendValidator()
	 */
	public function appendValidator(Phools_Validator_Interface $Validator)
	{
		$LastChild = $this->getLastChild();
		if ( ! $LastChild )
		{
			$this->setFirstChild($Validator);
			$this->setLastChild($Validator);
		}
		else {
			$LastChild->setNextSibling($Validator);
			$Validator->setPreviousSibling($LastChild);

			$this->setLastChild($Validator);
		}

		$Validator->setParentValidator($this);

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Validator_Interface
	 */
	protected $ParentValidator = null;

	/**
	 *
	 *
	 * @param Phools_Validator_Interface $ParentValidator
	 * @return Phools_Validator_Abstract
	 */
	protected function setParentValidator(Phools_Validator_Interface $ParentValidator)
	{
		$this->ParentValidator = $ParentValidator;
		return $this;
	}

	/**
	 *
	 * @return Phools_Validator_Interface
	 */
	public function getParentValidator()
	{
		return $this->ParentValidator;
	}

	/**
	 *
	 *
	 * @var Phools_Validator_Interface
	 */
	protected $PreviousSibling = null;

	/**
	 *
	 *
	 * @param Phools_Validator_Interface $PreviousSibling
	 * @return Phools_Validator_Abstract
	 */
	protected function setPreviousSibling(Phools_Validator_Interface $PreviousSibling)
	{
		$this->PreviousSibling = $PreviousSibling;
		return $this;
	}

	/**
	 *
	 * @return Phools_Validator_Interface
	 */
	public function getPreviousSibling()
	{
		return $this->PreviousSibling;
	}

	/**
	 *
	 *
	 * @var Phools_Validator_Interface
	 */
	protected $NextSibling = null;

	/**
	 *
	 *
	 * @param Phools_Validator_Interface $NextSibling
	 * @return Phools_Validator_Abstract
	 */
	protected function setNextSibling(Phools_Validator_Interface $NextSibling)
	{
		$this->NextSibling = $NextSibling;
		return $this;
	}

	/**
	 *
	 * @return Phools_Validator_Interface
	 */
	public function getNextSibling()
	{
		return $this->NextSibling;
	}

	/**
	 *
	 *
	 * @var Phools_Validator_Interface
	 */
	protected $FirstChild = null;

	/**
	 *
	 *
	 * @param Phools_Validator_Interface $FirstChild
	 * @return Phools_Validator_Abstract
	 */
	protected function setFirstChild(Phools_Validator_Interface $FirstChild)
	{
		$this->FirstChild = $FirstChild;
		return $this;
	}

	/**
	 *
	 * @return Phools_Validator_Interface
	 */
	public function getFirstChild()
	{
		return $this->FirstChild;
	}

	/**
	 *
	 *
	 * @var Phools_Validator_Interface
	 */
	protected $LastChild = null;

	/**
	 *
	 *
	 * @param Phools_Validator_Interface $LastChild
	 * @return Phools_Validator_Abstract
	 */
	protected function setLastChild(Phools_Validator_Interface $LastChild)
	{
		$this->LastChild = $LastChild;
		return $this;
	}

	/**
	 *
	 * @return Phools_Validator_Interface
	 */
	public function getLastChild()
	{
		return $this->LastChild;
	}

	/**
	 *
	 *
	 * @var bool
	 */
	protected $BreakChainOnFailure = false;

	/**
	 *
	 *
	 * @param bool $BreakChainOnFailure
	 * @return Phools_Validator_Abstract
	 */
	public function setBreakChainOnFailure($BreakChainOnFailure)
	{
		$this->BreakChainOnFailure = (bool) $BreakChainOnFailure;
		return $this;
	}

	/**
	 *
	 * @return bool
	 */
	protected function breakChainOnFailure()
	{
		return $this->BreakChainOnFailure;
	}

}
