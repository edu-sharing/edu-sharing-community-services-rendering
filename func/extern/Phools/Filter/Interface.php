<?php

/**
 * Filters are responsible to convert string-values to their expected types.
 * For example a string containing "123" could be filtered to the integer "123".
 *
 *
 */
interface Phools_Filter_Interface
{

	/**
	 * Filter given string.
	 *
	 * @param string $String
	 *
	 * @return mixed
	 */
	public function filter($String);

}
