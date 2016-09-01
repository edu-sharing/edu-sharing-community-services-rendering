<?php

/**
 *
 *
 *
 */
interface Phools_Net_Mime_Encoding_Interface
{

	/**
	 *
	 * @return string
	 */
	public function getName();

	/**
	 *
	 * @param string $String
	 *
	 * @return string
	 */
	public function encode($String);

	/**
	 *
	 * @param string $String
	 *
	 * @return string
	 */
	public function decode($String);

}
