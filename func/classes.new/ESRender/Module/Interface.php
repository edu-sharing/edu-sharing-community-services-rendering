<?php

/**
 *
 *
 *
 */
interface ESRender_Module_Interface
{

	/**
	 * Test if an object-instance exists. Returns true if instance exists,
	 * false otherwise.
	 *
	 * @param ESObject $ESObject
	 * @param ESObject $ESObject
	 * @param string $contentHash
	 *
	 * @return bool
	 */
	public function instanceExists(
		ESObject $ESObject);

	/**
	 * Create an object-instance. Return true on success, false on failure.
	 *
	 * @return bool
	 */
	public function createInstance(
		ESObject $ESObject);

	/**
	 * Process (display/download) a rendered object-instance.
	 *
	 * @return bool
	 */
	public function process(
		$p_kind,
		ESObject $ESObject);

	/**
	 *
	 * @return int
	 */
	public function getTimesOfUsage();

}
