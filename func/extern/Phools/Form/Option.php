<?php

/**
 *
 *
 *
 */
class Phools_Form_Option
implements Phools_Form_Option_Interface
{

	public function __construct($Name, $Value = '', $IsSelected = false)
	{
		$this
			->setName($Name)
			->setValue($Value)
			->setIsSelected($IsSelected);
	}

	public function populate(array $Values)
	{
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Name = '';

	/**
	 *
	 *
	 * @param string $Name
	 * @return Phools_Form_Option
	 */
	public function setName($Name)
	{
		$this->Name = (string) $Name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->Name;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Value = '';

	/**
	 *
	 *
	 * @param string $Value
	 * @return Phools_Form_Option
	 */
	public function setValue($Value)
	{
		$this->Value = (string) $Value;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->Value;
	}

	/**
	 *
	 *
	 * @var bool
	 */
	protected $IsSelected = false;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Option_Interface::setIsSelected()
	 */
	public function setIsSelected($IsSelected)
	{
		$this->IsSelected = (bool) $IsSelected;
		return $this;
	}

	/**
	 *
	 * @return bool
	 */
	public function isSelected()
	{
		return $this->IsSelected;
	}

}
