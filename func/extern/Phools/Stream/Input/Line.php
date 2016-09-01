<?php

/**
 *
 *
 *
 */
class Phools_Stream_Input_Line
extends Phools_Stream_Input_Buffer
{

	const CRLF = "\r\n";

	/**
	 *
	 * @param Phools_Stream_Input_Interface $InputStream
	 */
	public function __construct(Phools_Stream_Input_Interface $InputStream)
	{
		$this->setInputStream($InputStream);
	}

	public function __destruct()
	{
		$this->InputStream = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::eof()
	 */
	public function eof()
	{
		if ( ! $this->getInputStream()->eof() )
		{
			return false;
		}

		return parent::eof();
	}

	/**
	 *
	 */
	public function next()
	{
		$LineLength = $this->getLineLength();
		$Data = $this->getInputStream()->read($LineLength);

		$Data = '';
		while( false === strpos($Data, $this->getLineSeparator() ) )
		{
			$Data .= $this->getInputStream()->read(1);
		}

		$this->append($Data);
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $LineLength = 76;

	/**
	 *
	 *
	 * @param int $LineLength
	 * @return Phools_Stream_Input_Line
	 */
	public function setLineLength($LineLength)
	{
		assert( is_int($LineLength) );
		assert( 0 < $LineLength );

		$this->LineLength = (int) $LineLength;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getLineLength()
	{
		return $this->LineLength;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $LineSeparator = "\r\n";

	/**
	 *
	 *
	 * @param string $LineSeparator
	 * @return Phools_Stream_Input_Line
	 */
	public function setLineSeparator($LineSeparator)
	{
		assert( is_string($LineSeparator) );
		assert( 0 < strlen($LineSeparator) );

		$this->LineSeparator = (string) $LineSeparator;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getLineSeparator()
	{
		return $this->LineSeparator;
	}

}
