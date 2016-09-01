<?php

/**
 *
 *
 *
 */
interface Phools_Auth_Interface
{

	/**
	 *
	 * @param Phools_Auth_Adapter_Interface $Adapter
	 * @return Phools_Auth_Interface
	 */
	public function addAdapter(Phools_Auth_Adapter_Interface $Adapter);

	/**
	 *
	 * @param string $Identity
	 * @param string $Credential
	 * @param string $Salt
	 *
	 * @return bool
	 */
	public function authenticate($Identity, $Credential, $Salt = '');

	/**
	 *
	 * @param Phools_Auth_Plugin_Interface $Plugin
	 * @return Phools_Auth_Interface
	 */
	public function addPlugin(Phools_Auth_Plugin_Interface $Plugin);

}
