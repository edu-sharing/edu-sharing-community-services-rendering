<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_Reset
extends Phools_Form_Component_Abstract
{

	public function __construct($Name, $Value = 'reset')
	{
		parent::__construct($Name);

		$this->setValue($Value);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		return $Renderer->renderReset(
			$this->getPath(),
			$this->getValue());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::isValid()
	 */
	public function isValid()
	{
		$Validator = $this->getValidator();
		if ( $Validator )
		{
			return $Validator->validate($this->getValue());
		}

		return true;
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
	 * @return Phools_Form_Element_Submit
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
	protected function getValue()
	{
		return $this->Value;
	}

}
