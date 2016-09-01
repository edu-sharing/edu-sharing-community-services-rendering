<?php

/**
 *
 *
 */
interface Phools_Hashing_Interface
{

	/**
	 *
	 * @param string $Credential
	 * @param string $Salt
	 *
	 * @return string
	 */
	public function hash($Credential, $Salt = '');

}
