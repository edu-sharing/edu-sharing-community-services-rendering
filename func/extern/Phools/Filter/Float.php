<?php

/**
 * Filter given value to float.
 *
 *
 */
class Phools_Filter_Float
implements Phools_Filter_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Filter_Interface::filter()
	 */
	public function filter($String)
	{
		return (float) $String;
	}

}
