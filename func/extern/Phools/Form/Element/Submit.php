<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_Submit
extends Phools_Form_Component_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 */
	public function __construct($Name, $Value = 'submit')
	{
		parent::__construct($Name);

		$this->setValue($Value);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::populate()
	 */
	public function populate(array $Values)
	{
		// submit-elements shouldn't import a sumbitted value.
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		return $Renderer->renderSubmit(
			$this->getPath(),
			$this->getValue());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::isValid()
	 */
	public function isValid()
	{
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
