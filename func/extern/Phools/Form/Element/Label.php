<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_Label
extends Phools_Form_Component_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param Phools_Form_Component_Abstract $Component
	 */
	public function __construct($Name, Phools_Form_Component_Abstract $Component)
	{
		parent::__construct($Name);

		$this
			->setComponent($Component);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->Component = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		return $Renderer->renderLabel(
			$this->getPath(),
			$Component->getName());
	}

	/**
	 *
	 *
	 * @var Phools_Form_Component_Abstract
	 */
	protected $Component = null;

	/**
	 *
	 *
	 * @param Phools_Form_Component_Abstract $Component
	 * @return Phools_Form_Element_Label
	 */
	public function setComponent(Phools_Form_Component_Abstract $Component)
	{
		$this->Component = $Component;
		return $this;
	}

	/**
	 *
	 * @return Phools_Form_Component_Abstract
	 */
	protected function getComponent()
	{
		return $this->Component;
	}

}
