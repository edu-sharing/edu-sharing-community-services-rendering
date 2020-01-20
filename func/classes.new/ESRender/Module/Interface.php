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
	public function instanceExists();

	/**
	 * Create an object-instance. Return true on success, false on failure.
	 *
	 * @return bool
	 */
	public function createInstance();

	/**
	 * Process (display/download) a rendered object-instance.
	 *
	 * @return bool
	 */
	public function process($p_kind);

	/**
	 *
	 * @return int
	 */
	public function getTimesOfUsage();

}
