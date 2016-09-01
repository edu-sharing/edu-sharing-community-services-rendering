<?php

/**
 *
 *
 *
 */
abstract class Phools_Net_Smtp_Builder_Abstract
implements Phools_Net_Smtp_Builder_Interface
{

	/**
	 * Garbage collect.
	 *
	 */
	public function __destruct()
	{
		$this->Message = null;
	}

	/**
	 * Hold a reference to the message which is currently under construction.
	 *
	 * @var Phools_Net_Smtp_Message_Interface
	 */
	private $Message = null;

	/**
	 * Set the message which is currently under construction.
	 *
	 * @param Phools_Net_Smtp_Message_Interface $Message
	 *
	 * @return Phools_Net_Smtp_Builder_Abstract
	 */
	protected function setMessage(Phools_Net_Smtp_Message_Interface &$Message)
	{
		$this->Message = $Message;

		return $this;
	}

	/**
	 *
	 * @throws Phools_Net_Smtp_Exception
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	protected function &getMessage()
	{
		if ( ! $this->Message )
		{
			throw new Phools_Net_Smtp_Exception('No message set. Please start building a new message by calling the appropriate method (e.g. newMessage()) first.');
		}

		return $this->Message;
	}

}
