<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_List
extends Phools_Form_Component_Abstract
{

	public function populate(array $Values)
	{
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		return $Renderer->renderList(
			$this->getPath(),
			$this->isMultiple(),
			$this->getSize(),
			$this->getOptions());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::isValid()
	 */
	public function isValid()
	{
		$Result = true;

		$Validator = $this->getValidator();
		foreach( $this->getOptions() as $Option )
		{
			if ( $Option->isSelected() )
			{
				$Result &= $Validator->validate($Option->getValue());
			}
		}

		return $Result;
	}

	/**
	 *
	 *
	 * @var bool
	 */
	protected $IsMultiple = false;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Option_Interface::setIsMultiple()
	 */
	public function setIsMultiple($IsMultiple)
	{
		$this->IsMultiple = (bool) $IsMultiple;
		return $this;
	}

	/**
	 *
	 * @return bool
	 */
	public function isMultiple()
	{
		return $this->IsMultiple;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $Size = 2;

	/**
	 *
	 *
	 * @param int $Size
	 * @return Phools_Form_Element_List
	 */
	public function setSize($Size)
	{
		$this->Size = (int) $Size;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function getSize()
	{
		return $this->Size;
	}

	/**
	 *
	 * @var array
	 */
	private $Options = array();

	/**
	 *
	 * @param Phools_Form_Option_Interface $Option
	 */
	public function addOption(Phools_Form_Option_Interface $Option)
	{
		$this->Options[] = $Option;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->Options;
	}

}

