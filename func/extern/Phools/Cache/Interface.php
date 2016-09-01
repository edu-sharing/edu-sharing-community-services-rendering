<?php

/**
 *
 *
 *
 */
interface Phools_Cache_Interface
{

	/**
	 *
	 * @param string $Key
	 * @param string $Value
	 *
	 * @throws Phools_Cache_Exception
	 *
	 * @return Phools_Cache_Adapter_Interface
	 */
	public function addKey($Key, $Value);

	/**
	 *
	 * @param string $Key
	 * @param string $Value
	 *
	 * @return Phools_Cache_Adapter_Interface
	 */
	public function setKey($Key, $Value);

	/**
	 * Returns string for $Key or null if $Key does not exists.
	 *
	 * @param string $Key
	 *
	 * @return string | null
	 */
	public function getKey($Key);

	/**
	 *
	 * @param string $Key
	 *
	 * @return Phools_Cache_Interface
	 */
	public function unsetKey($Key);

	/**
	 * Clear complete cache.
	 *
	 * @return Phools_Cache_Interface
	 */
	public function clear();

}
