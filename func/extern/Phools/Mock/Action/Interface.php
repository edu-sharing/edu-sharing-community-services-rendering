<?php

/**
 *
 *
 *
 */
interface Phools_Mock_Action_Interface
{

	/**
	 *
	 * @param string $Method
	 * @param array $Arguments
	 *
	 * @throws Exception
	 * @throws Phools_Mock_Exception_InvalidActionException
	 *
	 * @return mixed
	 */
	public function onCall($Method, array $Arguments = array());

	/**
	 *
	 * @param string $Property
	 *
	 * @throws Exception
	 * @throws Phools_Mock_Exception_InvalidActionException
	 *
	 * @return mixed
	 */
	public function onGet($Property);

	/**
	 *
	 * @param string $Property
	 * @param mixed $Value
	 *
	 * @throws Exception
	 * @throws Phools_Mock_Exception_InvalidActionException
	 *
	 * @return mixed
	 */
	public function onSet($Property, $Value);

}
