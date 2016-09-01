<?php

/**
 *
 *
 *
 */
class Phools_Mock_Action_AssertTypeBoolean
extends Phools_Mock_Action_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Abstract::onSet()
	 */
	public function onSet($Property, $Value)
	{
		if ( is_bool($Value) )
		{
			return true;
		}

		return false;
	}

}
