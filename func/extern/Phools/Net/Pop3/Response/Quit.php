<?php

class Phools_Net_Pop3_Response_Quit
extends Phools_Net_Pop3_Response_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Response_Interface::receive()
	 */
	public function receive(Phools_Stream_Input_Interface $Input)
	{
		parent::receive($Input);

		$ServerGoodbye = $this->readLine($Input);
		$this->setServerGoodbye($ServerGoodbye);

		return $this;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $ServerGoodbye = '';

	/**
	 *
	 *
	 * @param string $ServerGoodbye
	 *
	 * @return Phools_Net_Pop3_Response_Quit
	 */
	protected function setServerGoodbye($ServerGoodbye)
	{
		assert( is_string($ServerGoodbye) );

		$this->ServerGoodbye = (string) $ServerGoodbye;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getServerGoodbye()
	{
		return $this->ServerGoodbye;
	}

}
