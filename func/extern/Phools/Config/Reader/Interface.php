<?php

/**
 * Config-reader read configuration-data from different sources.
 * 
 *
 */
interface Phools_Config_Reader_Interface
{
	
	/**
	 * Throw a Phools_Config_Reader_Exception if anything goes wrong while 
	 * reading the configuration.
	 * 
	 * @throws Phools_Config_Reader_Exception
	 * @return bool
	 */
	public function readConfig();
	
	/**
	 * 
	 * 
	 * @param Phools_Config_Interface $config
	 * @return Phools_Config_Reader_Interface
	 */
	public function setConfig(Phools_Config_Interface $Config);
	
	/**
	 * 
	 * 
	 * @return Phools_Config_Interface
	 */
	public function getConfig();
	
}
