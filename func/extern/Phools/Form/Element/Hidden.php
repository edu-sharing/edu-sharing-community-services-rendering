<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_Submit
extends Phools_Form_Component_Abstract
{

	public function __construct($Name, $Value = '')
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
		return $Renderer->renderHidden(
			$this->getPath(),
			$this->getValue());
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
