<?php

/**
 * Convert given value to a boolean.
 *
 *
 */
class Phools_Filter_Boolean
implements Phools_Filter_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Filter_Interface::filter()
	 */
	public function filter($String)
	{
		switch( strtolower($String) )
		{
			case 0:
			case null:
			case false:
			case '':
			case 'false':
			case '0':
			case 'off':
			case 'no':
			case 'none':
			case 'null':
				$Value = false;
				break;
			default:
				$Value = true;
		}

		return $Value;
	}

}
