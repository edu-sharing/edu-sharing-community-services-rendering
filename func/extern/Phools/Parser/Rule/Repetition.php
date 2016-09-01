<?php

/**
 *
 *
 *
 */
class Phools_Parser_Rule_Repetition
extends Phools_Parser_Rule_Abstract
{

	/**
	 *
	 * @param int $Min The required minimum of repetitions
	 * @param int $Max The maximum of repetitions
	 * @param Phools_Parser_Rule_Interface $Rule
	 */
	public function __construct($Min, $Max, $Rule)
	{
		$this
			->setMin($Min)
			->setMax($Max)
			->setRule($Rule);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Rule = null;
		$this->Max = null;
		$this->Min = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Rule_Interface::parse()
	 */
	public function parse(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer)
	{
		$Start = $InputBuffer->getPosition();

		$Repetitions = 0;
		while( $this->getRule()->parse($Parser, $InputBuffer) )
		{
			if ( $this->getMax() < $Repetitions )
			{
				$InputBuffer->seek($Start);

				return false;
			}

			$Repetitions += 1;
		}

		if ( $this->getMin() > $Repetitions )
		{
			$InputBuffer->seek($Start);

			return false;
		}

		return true;
	}

	/**
	 *
	 * @var int
	 */
	protected $Min = 0;

	/**
	 *
	 * @return Phools_Parser_Rule_Repetition
	 */
	public function setMin($Min)
	{
		assert( is_int($Min) );
		assert( 0 <= $Min );

		if ( $Min > $this->getMax() )
		{
			throw new Phools_Exception('Minimum cannot be greater than maximum.');
		}

		$this->Min = (int)$Min;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getMin()
	{
		return $this->Min;
	}

	/**
	 *
	 * @var int
	 */
	protected $Max = PHP_INT_MAX;

	/**
	 *
	 * @return Phools_Parser_Rule_Repetition
	 */
	public function setMax($Max)
	{
		assert( is_int($Max) );
		assert( 0 < $Max );

		if ( PHP_INT_MAX < $Max )
		{
			throw new Phools_Exception('Max-repetitions are limited by PHP_INT_MAX.');
		}

		if ( $Max < $this->getMin() )
		{
			throw new Phools_Exception('Maximum repetitions must be greater than min.');
		}

		$this->Max = (int)$Max;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getMax()
	{
		return $this->Max;
	}

	/**
	 *
	 * @var Phools_Parser_Rule_Interface
	 */
	private $Rule = '';

	/**
	 *
	 * @param Phools_Parser_Rule_Interface $Rule
	 */
	protected function setRule(Phools_Parser_Rule_Interface $Rule)
	{
		$this->Rule = $Rule;
		return $this;
	}

	/**
	 *
	 * @return Phools_Parser_Rule_Interface
	 */
	public function getRule()
	{
		return $this->Rule;
	}

}
