<?php

/**
 * Plugin-base for not having to implement all methods required by
 * Phools_Auth_Plugin_Interface.
 *
 */
abstract class Phools_Auth_Plugin_Abstract
implements Phools_Auth_Plugin_Interface
{

	/**
	 * Method-stub
	 *
	 * (non-PHPdoc)
	 * @see Phools_Auth_Plugin_Interface::preAuthentication()
	 */
	public function preAuthentication($Adapter, $Identity, $Credential, $Salt = '')
	{
	}

	/**
	 * Method-stub
	 *
	 * (non-PHPdoc)
	 * @see Phools_Auth_Plugin_Interface::onSuccess()
	 */
	public function onSuccess($Adapter, $Identity, $Credential, $Salt = '')
	{
	}

	/**
	 * Method-stub
	 *
	 * (non-PHPdoc)
	 * @see Phools_Auth_Plugin_Interface::onFailure()
	 */
	public function onFailure($Adapter, $Identity, $Credential, $Salt = '')
	{
	}

}
