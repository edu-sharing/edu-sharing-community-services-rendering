<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_Checkbox
extends Phools_Form_Component_Abstract
{

	public function populate(array $Values)
	{
		foreach( $this->getOptions() as $Option )
		{
			$Option->populate($Values);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		return $Renderer->renderCheckbox(
			$this->getPath(),
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
		if ( $Validator )
		{
			foreach( $this->getOptions() as $Option )
			{
				$Result &= $Validator->validate($Option->getValue());
			}
		}

		return $Result;
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
		// pre-filter option
		$Value = $Option->getValue();
		foreach( $this->getFilters() as $Filter )
		{
			$Value = $Filter->filter($Value);
		}

		$Option->setValue($Value);

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
