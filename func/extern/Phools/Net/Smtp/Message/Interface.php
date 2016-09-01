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
 */
interface Phools_Net_Smtp_Message_Interface
{

	/**
	 * Read message from $InputStream.
	 *
	 * @param Phools_Stream_Input_Interface $InputStream
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function read(Phools_Stream_Input_Interface $InputStream);

	/**
	 *
	 * @param Phools_Stream_Output_Interface $Output
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function write(Phools_Stream_Output_Interface $Output);

	/**
	 *
	 * @param Phools_Net_Smtp_Header_Interface $Header
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function addHeader(Phools_Net_Smtp_Header_Interface $Header);

	/**
	 *
	 *
	 * @param Phools_Net_Smtp_Header_Interface $Header
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function setHeader(Phools_Net_Smtp_Header_Interface $Header);

	/**
	 * Return the first header by $Name.
	 *
	 * @param string $Name
	 *
	 * @return Phools_Net_Smtp_Header_Interface
	 */
	public function getHeader($Name);

	/**
	 * Return all headers by $Name.
	 *
	 * @param string $Name
	 *
	 * @return array
	 */
	public function getHeaders($Name);

	/**
	 *
	 * @return array
	 */
	public function getAllHeaders();

	/**
	 * Set the message-date.
	 *
	 * @param DateTime $Date
	 *
	 * @return Phools_Net_Smtp_Header_Interface
	 */
	public function setDate(DateTime $Date);

	/**
	 * Get the message-date.
	 *
	 * @return DateTime
	 */
	public function getDate();

	/**
	 * Set this message's sender.
	 *
	 * @param Phools_Net_Smtp_Address_Mailbox $Sender
	 */
	public function setSender(Phools_Net_Smtp_Address_Mailbox $Sender);

	/**
	 * Return null if no sender present.
	 *
	 * @return Phools_Net_Smtp_Address_Mailbox
	 */
	public function getSender();

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Mailbox $Author
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function addAuthor(Phools_Net_Smtp_Address_Mailbox $Author);

	/**
	 *
	 * @return Phools_Net_Smtp_Header_AddressList
	 */
	public function getAuthors();

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Interface $ReplyTo
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function addReplyTo(Phools_Net_Smtp_Address_Interface $ReplyTo);

	/**
	 *
	 * @return Phools_Net_Smtp_Header_AddressList
	 */
	public function getReplyTos();

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Interface $Recipient
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function addRecipient(Phools_Net_Smtp_Address_Interface $Recipient);

	/**
	 *
	 * @return Phools_Net_Smtp_Header_AddressList
	 */
	public function getRecipients();

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Interface $Cc
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function addCc(Phools_Net_Smtp_Address_Interface $Cc);

	/**
	 *
	 * @return Phools_Net_Smtp_Header_AddressList
	 */
	public function getCcs();

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Interface $Bcc
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function addBcc(Phools_Net_Smtp_Address_Interface $Bcc);

	/**
	 *
	 * @return Phools_Net_Smtp_Header_AddressList
	 */
	public function getBccs();

	/**
	 * Set the message-subject.
	 *
	 * @param string $Subject
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function setSubject($Subject);

	/**
	 * Get the message-Subject.
	 *
	 * @return string
	 */
	public function getSubject();

	/**
	 * Set this message's text.
	 *
	 * @param string $Body
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function setText($Text = '');

	/**
	 * Get the message's text.
	 *
	 * @return string
	 */
	public function getText();

}
