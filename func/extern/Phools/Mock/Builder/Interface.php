<?php

/**
 *
 *
 *
 */
interface Phools_Mock_Builder_Interface
{

	/**
	 *
	 * @param string $Interface
	 *
	 * @return Phools_Mock_Builder_Interface
	 */
	public function mock($Interface);

	/**
	 *
	 * @return Phools_Mock_Object_Interface
	 */
	public function getMock();

	/**
	 *
	 * @param string $Method
	 * @param mixed $Value
	 */
	public function onCallReturnValue($Method, $Value);

	/**
	 *
	 * @param string $Method
	 * @param Exception $Exception
	 */
	public function onCallThrowException($Method, Exception $Exception);

	/**
	 *
	 * @param string $Property
	 * @param mixed $Value
	 */
	public function onGetReturnValue($Property, $Value);

	/**
	 *
	 * @param string $Property
	 * @param Exception $Exception
	 */
	public function onGetThrowException($Property, Exception $Exception);

	/**
	 *
	 * @param string $Property
	 * @param Exception $Exception
	 */
	public function onSetThrowException($Property, Exception $Exception);

}
