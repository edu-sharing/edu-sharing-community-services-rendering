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
 * @see http://www.ietf.org/rfc/rfc5321.txt (3.3.  Mail Transactions)
 *
 * There are three steps to SMTP mail transactions.  The transaction
 * starts with a MAIL command that gives the sender identification.  (In
 * general, the MAIL command may be sent only when no mail transaction
 * is in progress; see Section 4.1.4.)  A series of one or more RCPT
 * commands follows, giving the receiver information.  Then, a DATA
 * command initiates transfer of the mail data and is terminated by the
 * "end of mail" data indicator, which also confirms the transaction.
 *
 */
class Phools_Net_Smtp_Command_RcptTo
extends Phools_Net_Smtp_Command_Abstract
{

	/**
	 *
	 * @param string $Recipient
	 */
	public function __construct($Recipient)
	{
		$this->setRecipient($Recipient);
	}

	public function __destruct()
	{
		$this->Recipient = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface &$Output)
	{
		$Output->write('RCPT TO: <' . $this->getRecipient() . '>');
		$Output->write(Phools_Net_Smtp_Command_Interface::CRLF);

		return $this;
	}

	/**
	 * Hold the recipient's mailbox.
	 *
	 * @var string
	 */
	private $Recipient = '';

	/**
	 * Set the recipient's mailbox.
	 *
	 * @param string $Recipient
	 *
	 * @return Phools_Net_Smtp_Command_Rcpt
	 */
	public function setRecipient($Recipient)
	{
		$this->Recipient = (string) $Recipient;
		return $this;
	}

	/**
	 * Get the recipient's mailbox.
	 *
	 * @return string
	 */
	protected function getRecipient()
	{
		return $this->Recipient;
	}

}
