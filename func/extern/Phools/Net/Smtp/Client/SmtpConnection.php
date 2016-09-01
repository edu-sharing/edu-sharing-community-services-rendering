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
class Phools_Net_Smtp_Client_SmtpConnection
extends Phools_Net_Smtp_Client_Connection_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Client_Interface::initialize()
	 */
	public function initialize(Phools_Net_Connection_Interface $Connection)
	{
		try
		{
			$Connection = $this->getConnection();
			if ( $Connection->isEstablished() )
			{
				throw new Phools_Net_Smtp_Exception('Cannot use established connection.');
			}

			if ( ! $Connection->open() )
			{
				throw new Phools_Net_Smtp_Exception('Error opening connection.');
			}

			// read SERVER GREETING
			$Greeting = new Phools_Net_Smtp_Response_ServerGreeting();
			$Greeting->read($this);

			// send HELO
			$Identity = $this->getIdentity();

			$Command = new Phools_Net_Smtp_Response_Helo($Identity);
			$Command->send($this);

			$Response = new Phools_Net_Smtp_Response_Helo();
			$Response->read($this);
		}
		catch(Exception $Exception)
		{
			$Connection->close();

			return false;
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Client_Interface::send()
	 */
	public function send($Message, $From, $RcptTo)
	{
		// send MAIL FROM
		$Command = new Phools_Net_Smtp_Command_MailFrom($From);
		$Command->send($this);

		$Response = new Phools_Net_Smtp_Response_MailFrom();
		$Response->read($this);

		// send RCTP TO
		foreach( $Recipients as $Recipient )
		{
			$Command = new Phools_Net_Smtp_Command_RcptTo($Recipient);
			$Command->send($this);

			$Response = new Phools_Net_Smtp_Response_RcptTo();
			$Response->read($this);
		}

		// send DATA
		$Command = new Phools_Net_Smtp_Command_Data($Message);
		$Command->send($this);

		$Response = new Phools_Net_Smtp_Response_Data();
		$Response->read($this);

		// send MESSAGE
		$Command = new Phools_Net_Smtp_Command_Message($Message);
		$Command->send($this);

		$Response = new Phools_Net_Smtp_Response_Message();
		$Response->read($this);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Client_Interface::terminate()
	 */
	public function terminate()
	{
		if ( ! $this->isInitialized() )
		{
			throw new Exception('');
		}

		$Command = new Phools_Net_Smtp_Command_Quit();
		$Command->send($this);

		$Response = new Phools_Net_Smtp_Response_Quit();
		$Response->read($this);

		$Connection = $this->getConnection();
		if ( ! $Connection )
		{
			return false;
		}

		$Connection->close();

		$this->setConnection(null);

		return $this;
	}

}
