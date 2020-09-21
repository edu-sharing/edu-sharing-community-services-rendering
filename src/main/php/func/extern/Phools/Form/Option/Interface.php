<?php

/**
 *
 *
 *
 */
interface Phools_Form_Option_Interface
{

	/**
	 *
	 * @param string $Value
	 */
	public function setValue($Value);

	/**
	 *
	 * @return string
	 */
	public function getValue();

	/**
	 *
	 * @param bool $IsSelected
	 */
	public function setIsSelected($IsSelected);

	/**
	 *
	 * @return bool
	 */
	public function isSelected();

}
