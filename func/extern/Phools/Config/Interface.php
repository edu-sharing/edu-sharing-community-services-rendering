<?php

/**
 * The basic interface for configuration objects.
 * Extending ArrayAccess to allow smooth transition between traditional 
 * array-based configurations and this object-oriented one.
 * 
 *
 */
interface Phools_Config_Interface
extends ArrayAccess
{
	
	/**
	 * Test if config-param $Offset exists.
	 * 
	 * @Param string $Offset
	 * @return bool
	 */
	public function hasParam($Offset);

	/**
	 * Set config-param $Offset to $Value. Return object of 
	 * Phools_Config_Interface to allow method-chaining.
	 * 
	 * @Param string $name
	 * @Param string $Value
	 * @return Phools_Config_Interface
	 */
	public function setParam($name, $Value);

	/**
	 * Retrieve value for Param $Offset. Throws 
	 * Phools_Config_ParamNotFoundException when Param $Offset not found.
	 * 
	 * @Param string $name
	 * @throws Phools_Config_ParamNotFoundException
	 * @return string
	 */
	public function getParam($name);
	
	/**
	 * Return object of Phools_Config_Interface to allow method-chaining.
	 * 
	 * @Param string $Offset
	 * @return Phools_Config_Interface
	 */
	public function unsetParam($Offset);
	
}
