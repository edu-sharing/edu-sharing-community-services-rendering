<?php

/**
 *
 *
 *
 */
abstract class Phools_Form_Composite_Abstract
extends Phools_Form_Component_Abstract
{

	/**
	 * Render child-components.
	 *
	 * @param Phools_Form_Renderer_Interface $Renderer
	 *
	 * @return string
	 */
	protected function renderChildComponents(
		Phools_Form_Renderer_Interface $Renderer)
	{
		$String = '';

		$Component = $this->getFirstChild();
		while ( $Component )
		{
			$String .= $Component->render($Renderer);
			$Component = $Component->getNextComponent();
		}

		return $String;
	}

	/**
	 * Validate component.
	 *
	 * @return bool
	 */
	public function isValid()
	{
		$Result = true;

		$Component = $this->getFirstChild();
		while( $Component )
		{
			$Result &= $Component->isValid();
			$Component = $Component->getNextComponent();
		}

		return $Result;
	}

	/**
	 *
	 * @param Phools_Form_Component_Abstract $Component
	 *
	 * @return Phools_Form_Composite_Abstract
	 */
	public function prependComponent(Phools_Form_Component_Abstract $Component)
	{
		$Component->setParentComponent($this);

		$FirstChild = $this->getFirstChild();
		if ( $FirstChild )
		{
			$FirstChild->setPreviousComponent($Component);
			$Component->setNextComponent($LastChild);
		}
		else
		{
			$this->setLastChild($Component);
		}

		$this->setFirstChild($Component);

		return $this;
	}

	/**
	 *
	 * @param Phools_Form_Component_Abstract $Component
	 *
	 * @return Phools_Form_Composite_Abstract
	 */
	public function appendComponent(Phools_Form_Component_Abstract $Component)
	{
		$Component->setParentComponent($this);

		$LastChild = $this->getLastChild();
		if ( $LastChild )
		{
			$LastChild->setNextComponent($Component);
			$Component->setPreviousComponent($LastChild);
		}
		else
		{
			$this->setFirstChild($Component);
		}

		$this->setLastChild($Component);

		return $this;
	}

	/**
	 *
	 * @param Phools_Form_Component_Abstract $Component
	 * @param Phools_Form_Component_Abstract $Before
	 *
	 * @return Phools_Form_Composite_Abstract
	 */
	public function insertBefore(
		Phools_Form_Component_Abstract $Component,
		Phools_Form_Component_Abstract $Before)
	{
	}

	/**
	 *
	 * @param Phools_Form_Component_Abstract $Component
	 * @param Phools_Form_Component_Abstract $After
	 *
	 * @return Phools_Form_Composite_Abstract
	 */
	public function insertAfter(
		Phools_Form_Component_Abstract $Component,
		Phools_Form_Component_Abstract $After)
	{
	}

	/**
	 *
	 * @param Phools_Form_Component_Abstract $Component
	 *
	 * @return Phools_Form_Composite_Abstract
	 */
	public function remove(
		Phools_Form_Component_Abstract $Component)
	{
	}

	/**
	 *
	 * @param Phools_Form_Component_Abstract $Component
	 * @param Phools_Form_Component_Abstract $Replacement
	 *
	 * @return Phools_Form_Composite_Abstract
	 */
	public function replace(
		Phools_Form_Component_Abstract $Component,
		Phools_Form_Component_Abstract $Replacement)
	{
	}

	public function getChildComponent($Name)
	{
		$Component = $this->getFirstChild();
		while ( $Component )
		{
			if ( $Component->getName() == $Name )
			{
				return $Component;
			}

			$Component = $Component->getNextComponent();
		}

		throw new Phools_Form_Exception('Child-component "'.$Name.'" not found.');
	}

	/**
	 *
	 *
	 * @var Phools_Form_Component_Abstract
	 */
	private $FirstChild = null;

	/**
	 *
	 *
	 * @param Phools_Form_Component_Abstract $FirstChild
	 * @return Phools_Form_Composite_Abstract
	 */
	protected function setFirstChild(Phools_Form_Component_Abstract $FirstChild)
	{
		$this->FirstChild = $FirstChild;
		return $this;
	}

	/**
	 *
	 * @return Phools_Form_Component_Abstract
	 */
	public function getFirstChild()
	{
		return $this->FirstChild;
	}

	/**
	 *
	 *
	 * @var Phools_Form_Component_Abstract
	 */
	private $LastChild = null;

	/**
	 *
	 *
	 * @param Phools_Form_Component_Abstract $LastChild
	 * @return Phools_Form_Composite_Abstract
	 */
	protected function setLastChild(Phools_Form_Component_Abstract $LastChild)
	{
		$this->LastChild = $LastChild;
		return $this;
	}

	/**
	 *
	 * @return Phools_Form_Component_Abstract
	 */
	public function getLastChild()
	{
		return $this->LastChild;
	}

}
