<?php

class Phools_Form_Element_Fieldset
extends Phools_Form_Composite_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		$String = $Renderer->startFieldset($this->getPath());
		$String .= parent::renderChildComponents($Renderer);
		$String .= $Renderer->stopFieldset();

		return $String;
	}

}
