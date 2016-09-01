<?php

/**
 * This filter helps to trim a string by removing unwanted chars from the
 * beginning and end.
 *
 *
 */
class Phools_Filter_TrimString
implements Phools_Filter_Interface
{

	/**
	 *
	 * @param array $Charlist
	 */
	public function __construct(array $Charlist = array(" ", "\t", "\n", "\r", "\r\n", "\x00", "\x0B") )
	{
		$this->setCharlist($Charlist);
	}

	/**
	 * Free memory
	 */
	public function __destruct()
	{
		$this->Charlist = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Filter_Interface::filter()
	 */
	public function filter($String)
	{
		$Charlist = $this->getCharlist();
		if ( empty($Charlist) )
		{
			$Value = trim($String);
		}
		else {
			$Charlist = implode('', $Charlist);
			$Value = trim($String, $Charlist);
		}

		return $Value;
	}

	/**
	 *
	 *
	 * @var array
	 */
	private $Charlist = array();

	/**
	 *
	 *
	 * @param array $Charlist
	 * @return Phools_Filter_TrimString
	 */
	public function addChar($Char)
	{
		if ( ! in_array($Char, $Charlist) )
		{
			$this->Charlist[] = $Char;
		}

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getCharlist()
	{
		return $this->Charlist;
	}

}
