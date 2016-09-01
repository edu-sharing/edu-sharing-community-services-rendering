<?php

/**
 *
 *
 *
 */
class Phools_Form_Element_Form
extends Phools_Form_Composite_Abstract
{

	/**
	 *
	 * @param array $Values
	 */
	public function populate(array $Values)
	{
		$ChildComponent = $this->getFirstChild();
		while ( $ChildComponent )
		{
			$ChildComponent->populate($Values);
			$ChildComponent = $ChildComponent->getNextComponent();
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Form_Component_Abstract::render()
	 */
	public function render(Phools_Form_Renderer_Interface $Renderer)
	{
		$String = $Renderer->startForm($this->getPath(), $this->getAction(), $this->getMethod());
		$String .= parent::renderChildComponents($Renderer);
		$String .= $Renderer->stopForm();

		return $String;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Method = 'post';

	/**
	 *
	 *
	 * @param string $Method
	 * @return Phools_Form_Element_Form
	 */
	public function setMethod($Method)
	{
		$this->Method = (string) $Method;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getMethod()
	{
		return $this->Method;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Action = '';

	/**
	 *
	 *
	 * @param string $Action
	 * @return Phools_Form_Element_Form
	 */
	public function setAction($Action)
	{
		$this->Action = (string) $Action;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getAction()
	{
		return $this->Action;
	}

}
