<?php

/**
 *
 *
 *
 */
abstract class Phools_Mock_Object_Abstract
implements Phools_Mock_Object_Interface
{

	/**
	 *
	 *
	 * @var array
	 */
	protected $OnCall = array();

	/**
	 *
	 *
	 * @return mixed
	 */
	public function onCall($Method, Phools_Mock_Action_Interface $Action)
	{
		assert( is_string($Method) );

		if ( empty($this->OnCall[$Method]) )
		{
			$this->OnCall[$Method] = array();
		}

		array_push($this->OnCall[$Method], $Action);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Object_Interface::interceptCall()
	 */
	public function interceptCall($Method, array &$Arguments = array())
	{
		assert( is_string($Method) );
	}

	/**
	 *
	 *
	 * @var array
	 */
	protected $OnGet = array();

	/**
	 *
	 *
	 * @param array $OnGetActions
	 * @return Phools_Mock_Object_Abstract
	 */
	public function onGet($Property, Phools_Mock_Action_Interface $Action)
	{
		assert( is_string($Property) );

		if ( empty($this->OnGet[$Property]) )
		{
			$this->OnGet[$Property] = array();
		}

		array_push($this->OnGet[$Property], $Action);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Object_Interface::interceptGet()
	 */
	public function interceptGet($Property)
	{
		assert( is_string($Property) );
	}

	/**
	 *
	 * @var array
	 */
	private $OnSet = array();

	/**
	 *
	 *
	 * @param array $OnGetActions
	 * @return Phools_Mock_Object_Abstract
	 */
	public function onSet($Property, Phools_Mock_Action_Interface $Action)
	{
		assert( is_string($Property) );

		if ( empty($this->OnSet[$Property]) )
		{
			$this->OnSet[$Property] = array();
		}

		array_push($this->OnSet[$Property], $Action);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Object_Interface::interceptSet()
	 */
	public function interceptSet($Property, &$Value)
	{
		assert( is_string($Property) );
	}

}
