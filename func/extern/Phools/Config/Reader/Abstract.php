<?php

/**
 * 
 * 
 *
 */
abstract class Phools_Config_Reader_Abstract
implements Phools_Config_Reader_Interface
{
	
	public function __destruct() {
		$this->Config = null;
	}
	
	public function readIntoConfig(Phools_Config_Interface $Config) {
		$this
			->setConfig($Config)
			->read();
		
		return $this;
	}
	
	/**
	 *
	 *
	 * @var Phools_Config_Interface
	 */
	protected $Config = null;
	
	/**
	 * (non-PHPdoc)
	 * @see Phools_Config_Reader_Interface::setConfig()
	 */
	public function setConfig(Phools_Config_Interface $Config)
	{
		$this->Config = $Config;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Phools_Config_Reader_Interface::getConfig()
	 */
	public function getConfig()
	{
		return $this->Config;
	}
	
}
