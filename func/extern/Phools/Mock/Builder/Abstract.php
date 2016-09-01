<?php

/**
 *
 *
 *
 */
abstract class Phools_Mock_Builder_Abstract
implements Phools_Mock_Builder_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Builder_Interface::onCallReturnValue()
	 */
	public function onCallReturnValue($Method, $Value)
	{
		$Action = new Phools_Mock_Action_ReturnValue($Value);
		$this->getMock()->onCall($Method, $Action);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Builder_Interface::onCallThrowException()
	 */
	public function onCallThrowException($Method, Exception $Exception)
	{
		$Action = new Phools_Mock_Action_ThrowException($Exception);
		$this->getMock()->onCall($Method, $Action);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Builder_Interface::onGetThrowException()
	 */
	public function onGetThrowException($Property, Exception $Exception)
	{
		$Action = new Phools_Mock_Action_ThrowException($Exception);
		$this->getMock()->onGet($Property, $Action);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Builder_Interface::onSetThrowException()
	 */
	public function onSetThrowException($Property, Exception $Exception)
	{
		$Action = new Phools_Mock_Action_ThrowException($Exception);
		$this->getMock()->onSet($Property, $Action);

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Mock_Object_Interface
	 */
	protected $Mock = null;

	/**
	 *
	 *
	 * @param Phools_Mock_Object_Interface $Mock
	 * @return Phools_Mock_Builder_Abstract
	 */
	public function setMock(Phools_Mock_Object_Interface $Mock)
	{
		$this->Mock = $Mock;
		return $this;
	}

	/**
	 *
	 * @return Phools_Mock_Object_Interface
	 */
	public function getMock()
	{
		return $this->Mock;
	}

}
