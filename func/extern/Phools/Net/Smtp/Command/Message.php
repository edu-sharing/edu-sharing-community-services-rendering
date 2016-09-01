<?php

/**
 *
 *
 */
class Phools_Net_Smtp_Command_Message
extends Phools_Net_Smtp_Command_Abstract
{

	/**
	 *
	 * @param Phools_Net_Smtp_Message_Interface $Message
	 */
	public function __construct(Phools_Net_Smtp_Message_Interface $Message)
	{
		$this->setMessage($Message);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Command_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->Message = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface &$Output)
	{
		$Message = $this->getMessage();
		$Message->write($Output);

		$Output->write(Phools_Net_Smtp_Command_Interface::CRLF . '.');
		$Output->write(Phools_Net_Smtp_Command_Interface::CRLF);

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Smtp_Message_Interface
	 */
	protected $Message = null;

	/**
	 *
	 *
	 * @param Phools_Net_Smtp_Message_Interface $Message
	 * @return Phools_Net_Smtp_Command_Message
	 */
	public function setMessage(Phools_Net_Smtp_Message_Interface $Message)
	{
		$this->Message = $Message;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	protected function getMessage()
	{
		return $this->Message;
	}

}
