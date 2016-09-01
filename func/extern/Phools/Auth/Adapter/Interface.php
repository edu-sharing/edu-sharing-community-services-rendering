<?php

/**
 *
 *
 *
 */
interface Phools_Auth_Adapter_Interface
{

	/**
	 *
	 * @param string $Identity
	 * @param string $Credential
	 *
	 * @return bool
	 */
	public function authenticate($Identity, $Credential);

}
