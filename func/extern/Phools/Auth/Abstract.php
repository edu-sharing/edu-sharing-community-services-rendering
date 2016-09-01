<?php

abstract class Phools_Auth_Abstract
implements Phools_Auth_Interface
{

	/**
	 *
	 * @var array
	 */
	private $Adapters = array();

	/**
	 *
	 * @param Phools_Auth_Adapter_Interface $Adapter
	 */
	public function addAdapter(Phools_Auth_Adapter_Interface $Adapter)
	{
		$this->Adapters[] = $Adapter;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getAdapters()
	{
		return $this->Adapters;
	}

	/**
	 *
	 * @var array
	 */
	private $Plugins = array();

	/**
	 *
	 * @param Phools_Auth_Plugin_Interface $Plugin
	 */
	public function addPlugin(Phools_Auth_Plugin_Interface $Plugin)
	{
		$this->Plugins[] = $Plugin;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getPlugins()
	{
		return $this->Plugins;
	}

}
