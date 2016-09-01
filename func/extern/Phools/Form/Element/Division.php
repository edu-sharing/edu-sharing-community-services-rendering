<?php

class Phools_Form_Element_Division
extends Phools_Form_Composite_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		$String = $Renderer->startDivision($this->getPath());
		$String .= parent::renderChildComponents($Renderer);
		$String .= $Renderer->stopDivision();

		return $String;
	}

}
