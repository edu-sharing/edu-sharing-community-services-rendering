<?php

/**
 *
 *
 *
 */
interface Phools_Template_Interface
{

	/**
	 * Set optional theme.
	 *
	 * @param string $Name
	 * @return Phools_Template_Interface
	 */
	public function setTheme($Name = '');

	/**
	 * Render template.
	 *
	 * @param string $Name
	 * @param array $Params
	 *
	 * @return string
	 */
	public function render($Name, array $Params = array());

}
