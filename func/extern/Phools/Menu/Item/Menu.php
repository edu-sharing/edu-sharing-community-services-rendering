<?php

/**
 *
 *
 *
 */
class Phools_Menu_Item_Menu
extends Phools_Menu_Item_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Menu_Item_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->Items = null;

		parent::__destruct();
	}

	/**
	 *
	 * @var array
	 */
	private $Items = array();

	/**
	 * Prepend an item.
	 * Return $this to allow chaining.
	 *
	 * @param Phools_Menu_Item_Interface $Item
	 *
	 * @return Phools_Menu_Item_Menu
	 */
	public function prependItem(Phools_Menu_Item_Interface $Item)
	{
		array_unshift($this->Items, $Item);
		return $this;
	}

	/**
	 * Append an item.
	 * Return $this to allow chaining.
	 *
	 * @param Phools_Menu_Item_Interface $Item
	 *
	 * @return Phools_Menu_Item_Menu
	 */
	public function appendItem(Phools_Menu_Item_Interface $Item)
	{
		array_push($this->Items, $Item);
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getItems()
	{
		return $this->Items;
	}

}
