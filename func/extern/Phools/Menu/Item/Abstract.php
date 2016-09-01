<?php

/**
 * Convinient base-class to implement menu-Items upon.
 *
 *
 */
abstract class Phools_Menu_Item_Abstract
implements Phools_Menu_Item_Interface
{

	/**
	 *
	 * @param string $Name
	 */
	public function __construct($Name)
	{
		$this->setName($Name);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Classes = null;
		$this->Name = null;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Name = '';

	/**
	 * Set this Item's name.
	 *
	 * @param string $Name
	 * @return Phools_Menu_Item_Abstract
	 */
	public function setName($Name)
	{
		$this->Name = (string) $Name;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Menu_Item_Interface::getName()
	 */
	public function getName()
	{
		return $this->Name;
	}

	/**
	 *
	 *
	 * @var array
	 */
	protected $Classes = array();

	/**
	 *
	 *
	 * @param string $Name
	 *
	 * @return Phools_Menu_Item_Abstract
	 */
	public function addClass($Name)
	{
		// don't add twice
		if ( ! in_array($Name, $this->Classes) )
		{
			$this->Classes[] = $Name;
		}

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getClasses()
	{
		return $this->Classes;
	}

}
