<?php

class Phools_Net_Pop3_Response_Retr
extends Phools_Net_Pop3_Response_Abstract
{

	/**
	 *
	 * @param Phools_Stream_Output_Interface $MessageOutput
	 */
	public function __construct(Phools_Stream_Output_Interface $MessageOutput)
	{
		$this->setMessageOutput($MessageOutput);
	}

	/**
	 *
	 *
	 */
	public function __destruct()
	{
		$this->MessageOutput = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Response_Abstract::receive()
	 */
	public function receive(Phools_Stream_Input_Interface $Input)
	{
		parent::receive($Input);

		// read length
		$BytesToRead = $this->readDigits($Input);
		$this->readLine($Input);

		while( 0 < $BytesToRead )
		{
			$Data = $Input->peek($BytesToRead);
			$Input->forward( strlen($Data) );

			$BytesToRead -= $Output->write($Data);
		}

		$ExpectedDot = $this->readLine($Input);
		if ( '.' != $ExpectedDot )
		{
			throw new Phools_Net_Pop3_Exception_UnhandledResponseStatus('Expected dot, marking the end of message, not found.');
		}

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Output_Interface
	 */
	protected $MessageOutput = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Output_Interface $MessageOutput
	 *
	 * @return Phools_Net_Pop3_Response_Retr
	 */
	public function setMessageOutput(Phools_Stream_Output_Interface $MessageOutput)
	{
		$this->MessageOutput = $MessageOutput;

		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Output_Interface
	 */
	protected function getMessageOutput()
	{
		if ( ! $this->MessageOutput )
		{
			throw new Phools_Net_Pop3_Exception_Abstract('No message-stream set.');
		}

		return $this->MessageOutput;
	}

}
