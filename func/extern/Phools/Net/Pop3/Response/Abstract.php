<?php

/**
 *
 *
 *
 */
abstract class Phools_Net_Pop3_Response_Abstract
implements Phools_Net_Pop3_Response_Interface
{

	/**
	 * Helper-metho to read digits from $Input.
	 *
	 * @param Phools_Stream_Input_Interface $Input
	 *
	 * @return int
	 */
	protected function readDigits(Phools_Stream_Input_Interface $Input)
	{
		$Digits = '';
		while ( is_numeric( $Input->peek() ) )
		{
			$Digits .= $Input->forward();
		}

		return intval($Digits);
	}

	/**
	 * Helper-method to read a single line without ending line-break.
	 *
	 * @param Phools_Stream_Input_Interface $Input
	 * @throws Exception
	 */
	protected function readLine(Phools_Stream_Input_Interface $Input)
	{
		$Length = strpos($Input->peek(1024), Phools_Net_Pop3_Response_Interface::CRLF);
		if ( false === $Length )
		{
			throw new Exception('No line-break found.');
		}

		$Line = '';
		if ( 0 < $Length )
		{
			$Line = $Input->peek($Length);
			$Input->forward($Length);
		}

		$Input->forward(2);

		return $Line;
	}

	protected function skipWhitespace(Phools_Stream_Input_Interface $Input)
	{
		while( ' ' == $Input->peek() )
		{
			$Input->forward();
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Response_Interface::receive()
	 */
	public function receive(Phools_Stream_Input_Interface $Input)
	{
		$Status = $Input->peek(3);
		switch( $Status )
		{
			case '+OK':
				$Input->forward(3);
				$this->skipWhitespace($Input);
				break;

			default:
				$Line = $this->readLine($Input);
				throw new Phools_Net_Pop3_Exception_UnhandledResponseStatus($Line);
		}

		return $this;
	}

}
