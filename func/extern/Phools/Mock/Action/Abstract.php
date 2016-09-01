<?php

/**
 * Base-class providing method-stubs so extending classes do not have to
 * imlement unused methods by themselfs.
 *
 *
 */
abstract class Phools_Mock_Action_Abstract
implements Phools_Mock_Action_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Interface::onCall()
	 */
	public function onCall($Method, array $Arguments = array())
	{
		throw new Phools_Mock_Exception_InvalidActionException();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Interface::onGet()
	 */
	public function onGet($Property)
	{
		throw new Phools_Mock_Exception_InvalidActionException();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Interface::onSet()
	 */
	public function onSet($Property, $Value)
	{
		throw new Phools_Mock_Exception_InvalidActionException();
	}

}
