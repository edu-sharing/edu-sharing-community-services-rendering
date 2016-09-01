<?php

/*
 * This product Copyright 2010 metaVentis GmbH.  For detailed notice,
 * see the "NOTICE" file with this distribution.
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Command_MailFrom
extends Phools_Net_Smtp_Command_Abstract
{

	/**
	 *
	 * @param string $Sender
	 */
	public function __construct($Sender)
	{
		$this->setSender($Sender);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Command_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->Sender = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface &$Output)
	{
		$Data = 'MAIL FROM: <' . $this->getSender() . '>';
		$Output->write($Data . Phools_Net_Smtp_Command_Interface::CRLF);

		return $this;
	}

	/**
	 *
	 * @var string
	 */
	private $Sender = '';

	/**
	 *
	 * @param string $Sender
	 *
	 * @return Phools_Net_Smtp_Command_Mail
	 */
	public function setSender($Sender)
	{
		assert( is_string($Sender) );

		$this->Sender = $Sender;
	}

	/**
	 *
	 * @return string
	 */
	protected function getSender()
	{
		return $this->Sender;
	}

}
