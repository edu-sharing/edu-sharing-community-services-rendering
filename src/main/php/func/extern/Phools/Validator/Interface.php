<?php

/**
 * Validators are intended to validate incoming data.
 *
 *
 *
 */
interface Phools_Validator_Interface
{

	/**
	 * Test if given value is valid.
	 *
	 * @param mixed $Value
	 * @return bool
	 */
	public function validate($Value);

	/**
	 *
	 * @return Phools_Validator_Interface
	 */
	public function reset();

	/**
	 *
	 * @param Phools_Validator_Interface $Validator
	 *
	 * @return Phools_Validator_Interface
	 */
	public function appendValidator(Phools_Validator_Interface $Validator);

	/**
	 *
	 * @return array
	 */
	public function getErrorMessages();

}
