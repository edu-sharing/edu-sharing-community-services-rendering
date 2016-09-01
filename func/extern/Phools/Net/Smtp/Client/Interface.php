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
 * SMTP-client's have a lifetime-circle which starts to initialize() an
 * SMTP-session, continues with send()-ing of some messages ended by
 * terminate()-ing the started session.
 *
 *
 */
interface Phools_Net_Smtp_Client_Interface
{

	/**
	 * Set the identity of this client. It will be used when initializing as
	 * part of HELO- or EHLO-command.
	 *
	 * @param string $Identity
	 *
	 * @return Phools_Net_Smtp_Client_Interface
	 */
	public function setIdentity($Identity);

	/**
	 * Lifetime method.
	 *
	 * Initialize a SMTP-session.
	 *
	 * @see http://www.ietf.org/rfc/rfc5321.txt (3.2.  Client Initiation)
	 *
	 * Once the server has sent the greeting (welcoming) message and the
	 * client has received it, the client normally sends the EHLO command to
	 * the server, indicating the client's identity.  In addition to opening
	 * the session, use of EHLO indicates that the client is able to process
	 * service extensions and requests that the server provide a list of the
	 * extensions it supports.  Older SMTP systems that are unable to
	 * support service extensions, and contemporary clients that do not
	 * require service extensions in the mail session being initiated, MAY
	 * use HELO instead of EHLO.  Servers MUST NOT return the extended EHLO-
	 * style response to a HELO command.  For a particular connection
	 * attempt, if the server returns a "command not recognized" response to
	 * EHLO, the client SHOULD be able to fall back and send HELO.
	 *
	 * In the EHLO command, the host sending the command identifies itself;
	 * the command may be interpreted as saying "Hello, I am <domain>" (and,
	 * in the case of EHLO, "and I support service extension requests").
	 *
	 * @return bool
	 */
	public function initialize();

	/**
	 *
	 * @return bool
	 */
	public function isInitialized();

	/**
	 * Lifetime method.
	 *
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
	 * @param string $Message
	 * @param string $From
	 * @param array $To
	 *
	 * @return array
	 */
	public function send($Message, $From, array $To);

	/**
	 * Lifetime method.
	 *
	 * Terminate current SMTP-session.
	 *
	 * @return bool
	 */
	public function terminate();

	/**
	 * To be utilized by SMTP-commands.
	 *
	 * @param string $Data
	 *
	 * @throws Phools_Net_Smtp_Exception
	 *
	 * @return Phools_Net_Smtp_Client_Interface
	 */
	public function writeLine($Data);

	/**
	 * To be utilized by SMTP-commands.
	 *
	 * @throws Phools_Net_Smtp_Exception
	 *
	 * @return string
	 */
	public function readLine();

}
