<?php

/**
 *
 *
 *
 */
abstract class Phools_Parser_Packrat
extends Phools_Parser_Abstract
{

	/**
	 *
	 *
	 */
	private $Packrats = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Abstract::parse()
	 */
	public function parse($Name, Phools_Stream_Input_Buffer &$InputBuffer)
	{
		$Start = $InputBuffer->getPosition();

		// init cache
		if ( ! isset($this->Packrats[$Start]) )
		{
			$this->Packrats[$Start] = array();
		}

		// use cache if possible
		if ( isset( $this->Packrats[$Start][$Name]) )
		{
			if ( $this->Packrats[$Start][$Name]['matched'] )
			{
				$Length = $this->Packrats[$Start][$Name]['length'];
				$InputBuffer->forward($Length);

				return true;
			}

			return false;
		}

		if ( ! parent::parse($Name, $InputBuffer) )
		{
			// no match -> cache failure
			$this->Packrats[$Start][$Name] = array(
				'matched' => false,
			);

			return false;
		}

		// fill cache
		$Length = $InputBuffer->getPosition() - $Start;
		$this->Packrats[$Start][$Name] = array(
			'matched' => true,
			'length' => $Length,
		);

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Abstract::consume()
	 */
	public function consume(Phools_Stream_Input_Buffer &$InputBuffer)
	{
		$this->Packrats = array();

		return parent::consume($InputBuffer);
	}

}
