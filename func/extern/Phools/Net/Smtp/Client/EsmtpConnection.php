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
class Phools_Net_Smtp_Client_EsmtpConnection
extends Phools_Net_Smtp_Client_Connection_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Client_Interface::initialize()
	 */
	public function initialize()
	{
		try
		{
			$Connection = $this->getConnection();
			if ( $Connection->isEstablished() )
			{
				throw new Phools_Net_Smtp_Exception('Connection already established.');
			}

			if ( ! $Connection->open() )
			{
				throw new Phools_Net_Smtp_Exception('Error opening connection.');
			}

			// read SERVER GREETING
			$Greeting = new Phools_Net_Smtp_Response_ServerGreeting();
			$Greeting->read($this);

			// send EHLO
			$Command = new Phools_Net_Smtp_Command_Ehlo($this->getIdentity());

			foreach( $this->getExtensions() as $Extension )
			{
				$Extension->preSendCommand($this, $Command);
			}

			$Command->send($this);

			$Response = new Phools_Net_Smtp_Response_Ehlo();
			$Response->read($this);

			foreach( $this->getExtensions() as $Extension )
			{
				$Extension->postSentCommand($this, $Command, $Response);
			}
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
	public function send($Message, $From, array $Recipients)
	{
		// send MAIL FROM
		$Command = new Phools_Net_Smtp_Command_MailFrom($From);
		foreach( $this->getExtensions() as $Extension )
		{
			$Extension->preSendCommand($this, $Command);
		}

		$Command->send($this);

		$Response = new Phools_Net_Smtp_Response_MailFrom();
		$Response->read($this);

		foreach( $this->getExtensions() as $Extension )
		{
			$Extension->postSentCommand($this, $Command, $Response);
		}

		// send RCTP TO
		foreach( $Recipients as $Recipient )
		{
			$Command = new Phools_Net_Smtp_Command_RcptTo($Recipient);
			foreach( $this->getExtensions() as $Extension )
			{
				$Extension->preSendCommand($this, $Command);
			}

			$Command->send($this);

			$Response = new Phools_Net_Smtp_Response_RcptTo();
			$Response->read($this);

			foreach( $this->getExtensions() as $Extension )
			{
				$Extension->postSentCommand($this, $Command, $Response);
			}
		}

		// send DATA
		$Command = new Phools_Net_Smtp_Command_Data($Message);
		foreach( $this->getExtensions() as $Extension )
		{
			$Extension->preSendCommand($this, $Command);
		}

		$Command->send($this);

		$Response = new Phools_Net_Smtp_Response_Data();
		$Response->read($this);

		foreach( $this->getExtensions() as $Extension )
		{
			$Extension->postSentCommand($this, $Command, $Response);
		}

		// send MESSAGE
		$Command = new Phools_Net_Smtp_Command_Message($Message);
		foreach( $this->getExtensions() as $Extension )
		{
			$Extension->preSendCommand($this, $Command);
		}

		$Command->send($this);

		$Response = new Phools_Net_Smtp_Response_Message();
		$Response->read($this);

		foreach( $this->getExtensions() as $Extension )
		{
			$Extension->postSentCommand($this, $Command, $Response);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Client_Interface::terminate()
	 */
	public function terminate()
	{
		$Connection = $this->getConnection();
		if ( ! $Connection->isEstablished() )
		{
			return $this;
		}

		$Command = new Phools_Net_Smtp_Command_Quit();
		foreach( $this->getExtensions() as $Extension )
		{
			$Extension->preSendCommand($this, $Command);
		}

		$Command->send($this);

		$Response = new Phools_Net_Smtp_Response_Quit();
		$Response->read($this);

		foreach( $this->getExtensions() as $Extension )
		{
			$Extension->postSentCommand($this, $Command, $Response);
		}

		$Connection = $this->getConnection();
		if ( $Connection->isEstablished() )
		{
			$Connection->close();
		}

		return $this;
	}

	/**
	 *
	 * @var array
	 */
	private $Extensions = array();

	/**
	 *
	 * @param Phools_Net_Smtp_Extension_Interface $Extension
	 *
	 * @throws Phools_Net_Smtp_Extension
	 *
	 * @return Phools_Net_Smtp_Client_Smtp
	 */
	public function prependExtension(
		Phools_Net_Smtp_Extension_Interface $Extension)
	{
		foreach( $this->getExtensions() as $ExistingExtension )
		{
			if ( $ExistingExtension->getKeyword() == $Extension->getKeyword() )
			{
				throw new Phools_Net_Smtp_Exception('Extension already set.');
			}
		}

		array_unshift($this->Extensions, $Extension);

		return $this;
	}

	/**
	 *
	 * @param Phools_Net_Smtp_Extension_Interface $Extension
	 *
	 * @throws Phools_Net_Smtp_Extension
	 *
	 * @return Phools_Net_Smtp_Client_Smtp
	 */
	public function appendExtension(
		Phools_Net_Smtp_Extension_Interface $Extension)
	{
		foreach( $this->getExtensions() as $ExistingExtension )
		{
			if ( $ExistingExtension->getKeyword() == $Extension->getKeyword() )
			{
				throw new Phools_Net_Smtp_Exception('Extension already set.');
			}
		}

		array_push($this->Extensions, $Extension);

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getExtensions()
	{
		return $this->Extensions;
	}

}
