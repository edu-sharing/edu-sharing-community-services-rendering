<?php

/**
 * Auth-plugins allow to extend the authentication process.
 *
 * A possible use-case would be the transition to a different hashing alorithm,
 * e.g. MD5 to SHA512. Another use-case would be to synchronize external
 * identities (e.g. from external LDAP) to the database in the local system.
 *
 *
 */
interface Phools_Auth_Plugin_Interface
{

	/**
	 * To be called multiple times for every adapter.
	 *
	 * @param Phools_Auth_Adapter_Interface $Adapter
	 * @param string $Identity
	 * @param string $Credential
	 * @param string $Salt
	 */
	public function preAuthentication(
		Phools_Auth_Adapter_Interface $Adapter,
		$Identity,
		$Credential,
		$Salt = '');

	/**
	 * To be called once after successfully authenticating the identity.
	 *
	 * @param Phools_Auth_Adapter_Interface $Adapter
	 * @param string $Identity
	 * @param string $Credential
	 * @param string $Salt
	 */
	public function onSuccess(
		Phools_Auth_Adapter_Interface $Adapter,
		$Identity,
		$Credential,
		$Salt = '');

	/**
	 * To be called once after a failing attempt to authenticat the identity.
	 *
	 * @param Phools_Auth_Adapter_Interface $Adapter
	 * @param string $Identity
	 * @param string $Credential
	 * @param string $Salt
	 */
	public function onFailure(
		Phools_Auth_Adapter_Interface $Adapter,
		$Identity,
		$Credential,
		$Salt = '');

}
