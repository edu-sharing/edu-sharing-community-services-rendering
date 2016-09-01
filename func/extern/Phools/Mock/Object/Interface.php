<?php

/**
 *
 *
 *
 */
interface Phools_Mock_Object_Interface
{

	/**
	 *
	 * @param string $Method
	 * @param Phools_Mock_Action_Interface $Action
	 *
	 * @return Phock_Mock_Interface
	 */
	public function onCall($Method, Phools_Mock_Action_Interface $Action);

	/**
	 *
	 * @param string $Method
	 * @param array $Arguments
	 *
	 * @throws Exception
	 *
	 * @return mixed
	 */
	public function interceptCall($Method, array &$Arguments = array());

	/**
	 *
	 * @param string $Property
	 * @param Phools_Mock_Action_Interface $Action
	 *
	 * @return Phock_Mock_Interface
	 */
	public function onGet($Property, Phools_Mock_Action_Interface $Action);

	/**
	 *
	 * @param string $Property
	 *
	 * @throws Exception
	 *
	 * @return mixed
	 */
	public function interceptGet($Property);

	/**
	 *
	 * @param string $Property
	 * @param Phools_Mock_Action_Interface $Action
	 *
	 * @return Phock_Mock_Interface
	 */
	public function onSet($Property, Phools_Mock_Action_Interface $Action);

	/**
	 *
	 * @param string $Property
	 * @param mixed $Value
	 *
	 * @throws Exception
	 *
	 * @return mixed
	 */
	public function interceptSet($Property, &$Value);

}
