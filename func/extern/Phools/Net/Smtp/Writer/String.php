<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Writer_String
extends Phools_Net_Smtp_Writer_Abstract
{

	/**
	 *
	 * @param string $Target
	 */
	public function __construct(&$Target)
	{
		$this->setTarget($Target);
	}

	public function __destruct()
	{
		$this->Target = null;
	}

	/**
	 *
	 *
	 */
	public function flush()
	{
		return $this->Target;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Target = '';

	/**
	 *
	 *
	 * @param string $Target
	 * @return Phools_Net_Smtp_Writer_String
	 */
	public function setTarget(&$Target)
	{
		$this->Target = $Target;

		return $this;
	}

	public function append($String)
	{
		$this->Target .= $String;

		return $this;
	}

}
