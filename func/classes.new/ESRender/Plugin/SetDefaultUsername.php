<?php

/**
 * Overwrite given username by default.
 *
 */
class ESRender_Plugin_SetDefaultUsername
extends ESRender_Plugin_Abstract
{

	/**
	 * Hold the default username.
	 *
	 * @var string
	 */
	private $username = '';

	/**
	 *
	 * @param string $username the default username to set.
	 */
	public function __construct($username)
	{
		$this->username = $username;
	}

	/**
	 * Set username after loading repository-config.
	 *
	 * (non-PHPdoc)
	 * @see ESRender_Plugin_Abstract::postLoadRepository()
	 */
	public function postLoadRepository(
		&$data)
	{
        $data->user->authorityName = $this->username;
	}

}
