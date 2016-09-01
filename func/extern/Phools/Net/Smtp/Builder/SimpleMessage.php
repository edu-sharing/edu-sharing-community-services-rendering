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
class Phools_Net_Smtp_Builder_SimpleMessage
extends Phools_Net_Smtp_Builder_Abstract
{

	/**
	 *
	 * @param string $Address
	 * @param string $Name
	 */
	public function newMessage($Address, $Name = '')
	{
		$Author = new Phools_Net_Smtp_Address_Mailbox($Address, $Name);

		$Message = new Phools_Net_Smtp_Message_Simple($Author);

		$Date = new DateTime();
		$Message->setDate($Date);

		$this->setMessage($Message);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::buildMessage()
	 */
	public function buildMessage()
	{
		return $this->getMessage();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::setSender()
	 */
	public function sender($Address, $Name = '')
	{
		$this->getMessage()->setSender(
			new Phools_Net_Smtp_Address_Mailbox($Address, $Name));

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::addAuthor()
	 */
	public function from($Address, $Name = '')
	{
		$this->getMessage()->addAuthor(
			new Phools_Net_Smtp_Address_Mailbox($Address, $Name));

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::addAuthor()
	 */
	public function replyTo($Address, $Name = '')
	{
		$this->getMessage()->setReplyTo(
			new Phools_Net_Smtp_Address_Mailbox($Address, $Name));

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::addRecipient()
	 */
	public function to($Address, $Name = '')
	{
		$this->getMessage()->addRecipient(
			new Phools_Net_Smtp_Address_Mailbox($Address, $Name));

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::cc()
	 */
	public function cc($Address, $Name = '')
	{
		$this->getMessage()->addCc(
			new Phools_Net_Smtp_Address_Mailbox($Address, $Name));

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::bcc()
	 */
	public function bcc($Address, $Name = '')
	{
		$this->getMessage()->addBcc(
			new Phools_Net_Smtp_Address_Mailbox($Address, $Name));

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::subject()
	 */
	public function subject($Subject)
	{
		$this->getMessage()->setSubject($Subject);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Builder_Interface::text()
	 */
	public function text($Text)
	{
		$this->getMessage()->setText($Text);

		return $this;
	}

	/**
	 *
	 * @param string $MessageId
	 */
	public function inReplyTo($MessageId)
	{
		$this->getMessage()->addInReplyTo($MessageId);

		return $this;
	}

	/**
	 *
	 * @param string $MessageId
	 */
	public function references($MessageId)
	{
		$this->getMessage()->addReference($MessageId);

		return $this;
	}

}
