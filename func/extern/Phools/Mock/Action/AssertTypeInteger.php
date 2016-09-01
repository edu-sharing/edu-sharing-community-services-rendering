<?php

/**
 *
 *
 *
 */
class Phools_Mock_Action_AssertTypeInteger
extends Phools_Mock_Action_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Abstract::onSet()
	 */
	public function onSet($Property, $Value)
	{
		if ( is_int($Value) )
		{
			return true;
		}

		return false;
	}

}
