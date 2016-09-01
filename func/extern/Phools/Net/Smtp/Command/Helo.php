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
 */
class Phools_Net_Smtp_Command_Helo
extends Phools_Net_Smtp_Command_Abstract
{

	/**
	 *
	 * @param string $Identity
	 * @param string $Name
	 */
	public function __construct($Identity)
	{
		$this->setIdentity($Identity);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Command_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->Identity = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface &$Output)
	{
		$Data = 'HELO '.$this->getIdentity();
		$Output->write($Data . Phools_Net_Smtp_Command_Interface::CRLF);

		return $this;
	}

	/**
	 *
	 * @var string
	 */
	private $Identity = '';

	/**
	 *
	 * @param string $Identity
	 *
	 * @return Phools_Net_Smtp_Command_Helo
	 */
	public function setIdentity($Identity)
	{
		assert( is_string($Identity) );

		$this->Identity = $Identity;
	}

	/**
	 *
	 * @return string
	 */
	protected function getIdentity()
	{
		return $this->Identity;
	}

}
