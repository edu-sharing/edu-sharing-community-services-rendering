<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_Radio
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
		return $Renderer->renderRadio($this->getPath(), $this->getOptions());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::isValid()
	 */
	public function isValid()
	{
		$Validator = $this->getValidator();
		return $Validator->validate($this->getValue());
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
	protected function getOptions()
	{
		return $this->Options;
	}

}
