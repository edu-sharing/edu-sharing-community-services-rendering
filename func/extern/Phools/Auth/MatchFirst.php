<?php

/**
 * Requires one adapter to authenticate the given identity.
 *
 *
 */
class Phools_Auth_MatchFirst
extends Phools_Auth_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Auth_Interface::authenticate()
	 */
	public function authenticate($Identity, $Credential, $Salt = '')
	{
		foreach( $this->getAdapters() as $Adapter )
		{
			foreach( $this->getPlugins() as $Plugin )
			{
				$Plugin->preAuthenticate($Adapter, $Identity, $Credential, $Salt);
			}

			if ( $Adapter->authenticate($Identity, $Credential, $Salt) )
			{
				foreach( $this->getPlugins() as $Plugin )
				{
					$Plugin->onSuccess($Adapter, $Identity, $Credential, $Salt);
				}

				return true;
			}

			foreach( $this->getPlugins() as $Plugin )
			{
				$Plugin->onFailure($Adapter, $Identity, $Credential, $Salt);
			}
		}

		return false;
	}

}
