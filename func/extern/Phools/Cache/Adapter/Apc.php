<?php

class Phools_Cache_Adapter_Apc
extends Phools_Cache_Adapter_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Cache_Adapter_Interface::setKey()
	 */
	public function setKey($Key, $Value)
	{
		apc_store($Key, $Value);
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Cache_Adapter_Interface::getKey()
	 */
	public function getKey($Key)
	{
		$Value = apc_fetch($Key);
		return $Value;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Cache_Adapter_Interface::unsetKey()
	 */
	public function unsetKey($Key)
	{
		apc_delete($Key);
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Cache_Adapter_Interface::clear()
	 */
	public function clear()
	{
		apc_clear_cache();
		return $this;
	}

}
