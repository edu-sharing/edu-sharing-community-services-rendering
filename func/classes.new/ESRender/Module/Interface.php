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
	 * @param array $requestData
	 * @param string $contentHash
	 *
	 * @return bool
	 */
	public function instanceExists(
		ESObject $ESObject,
		array $requestData,
        $contentHash);

	/**
	 * Create an object-instance. Return true on success, false on failure.
	 *
	 * @return bool
	 */
	public function createInstance(
		array $requestData);

	/**
	 * Process (display/download) a rendered object-instance.
	 *
	 * @return bool
	 */
	public function process(
		$p_kind,
		array $requestData);

	/**
	 * Get the application this module belongs to.
	 *
	 * @return ESRender_Application_Interface
	 */
	public function getRenderApplication();

	/**
	 *
	 * @return int
	 */
	public function getTimesOfUsage();

}
