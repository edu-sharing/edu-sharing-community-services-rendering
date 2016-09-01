<?php

class Phools_Net_Pop3_Response_List
extends Phools_Net_Pop3_Response_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Response_Abstract::receive()
	 */
	public function receive(Phools_Stream_Input_Interface $Input)
	{
		parent::receive($Input);

		$Line = $this->readLine($Input);
var_dump($Line);

		return $this;
	}

}
