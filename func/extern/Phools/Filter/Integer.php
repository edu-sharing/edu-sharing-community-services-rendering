<?php

/**
 * Filter given value to int.
 *
 *
 */
class Phools_Filter_Integer
implements Phools_Filter_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Filter_Interface::filter()
	 */
	public function filter($String)
	{
		return (int) $String;
	}

}
