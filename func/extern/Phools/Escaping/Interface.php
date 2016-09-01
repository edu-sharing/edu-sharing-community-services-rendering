<?php

/**
 * 
 * 
 *
 */
interface Phools_Escaping_Interface
{
	
	/**
	 * 
	 * @param string $String
	 * @return string
	 */
	public function escape($String);
	
	/**
	 * 
	 * @param string $String
	 * @return string
	 */
	public function unescape($String);
	
}
