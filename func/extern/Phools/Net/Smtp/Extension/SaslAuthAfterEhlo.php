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
class Phools_Net_Smtp_Extension_SaslAuthAfterEhlo
extends Phools_Net_Smtp_Extension_Abstract
implements
	Phools_Net_Sasl_Client_Interface
{

	/**
	 *
	 * @param Phools_Net_Sasl_Mechanism_Interface $Mechanism
	 */
	public function __construct(Phools_Net_Sasl_Mechanism_Interface $Mechanism)
	{
		parent::__construct('AUTH');

		$this->setMechanism($Mechanism);
	}

	/**
	 * Free mechanism
	 *
	 */
	public function __destruct()
	{
		$this->Mechanism = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Extension_Abstract::postSentCommand()
	 */
	public function postSentCommand(
		Phools_Net_Smtp_Client_Interface $Client,
		Phools_Net_Smtp_Command_Interface $Command,
		Phools_Net_Smtp_Response_Interface $Response)
	{
		if ( 'EHLO' == $Command->getName() )
		{
			$this->bindClient($Client);

			try
			{
				$Mechanism = $this->getMechanism();
				if ( ! $Mechanism )
				{
					throw new Phools_Net_Smtp_Exception('No mechanism set.');
				}

				if ( ! $Mechanism->authenticate($this) )
				{
					throw new Phools_Net_Smtp_Exception('Could not authenticate.');
				}
			}
			catch ( Exception $Exception )
			{
				$this->releaseClient();

				throw $Exception;
			}

			$this->releaseClient();
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Sasl_Client_Interface::startSaslExchange()
	 */
	public function startSaslExchange($Mechanism, $Challenge = '')
	{
		$Client = $this->getClient();
		if ( ! $Client )
		{
			throw new Phools_Net_Smtp_Exception('No client bound.');
		}

		$Data = 'AUTH ' . $Mechanism;
		if ( $Challenge )
		{
			$Data .= ' ' . base64_encode($Challenge);
		}

		$Client->writeLine($Data);
		$Response = $Client->readLine();

		$Code = substr($Response, 0, 3);
		switch( $Code )
		{
			case '235':							// Authentication successful
				$Response = substr($Response, 4);
				break;
			case '334':							// Continue
				$Response = substr($Response, 4);
				break;
			case '503':
				throw new Phools_Net_Smtp_Exception('Extension "AUTH" not available.');
				break;
			case '504':
				throw new Phools_Net_Smtp_Exception('"AUTH"-mechanism "'.$Mechanism.'" not available.');
				break;
			default:
				throw new Phools_Net_Smtp_Exception('Unhandled response-code "'.$Code.'"');
		}

		$Response = base64_decode($Response);

		return $Response;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Sasl_Client_Interface::sendSaslCommand()
	 */
	public function sendSaslCommand($Data)
	{
		$Client = $this->getClient();
		if ( ! $Client )
		{
			throw new Phools_Net_Smtp_Exception('No client bound.');
		}

		$Data = base64_encode($Data);

		$Client->writeLine($Data);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Sasl_Client_Interface::readSaslResponse()
	 */
	public function readSaslResponse()
	{
		$Client = $this->getClient();
		if ( ! $Client )
		{
			throw new Phools_Net_Smtp_Exception('No client bound.');
		}

		$Response = $Client->readLine();

		$Code = substr($Response, 0, 3);
		switch( $Code )
		{
			case '334':
				$Response = substr($Response, 4);
				break;
			case '503':
				throw new Phools_Net_Smtp_Exception('Response-code "'.$Code.'"');
				break;
			default:
				throw new Phools_Net_Smtp_Exception('Unhandled response-code "'.$Code.'"');
		}

		$Response = base64_decode($Response);

		return $Response;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Sasl_Client_Interface::finalizeSaslExchange()
	 */
	public function finalizeSaslExchange()
	{
		$Client = $this->getClient();
		if ( ! $Client )
		{
			throw new Phools_Net_Smtp_Exception('No client bound.');
		}

		$Response = $Client->readLine();

		$Code = substr($Response, 0, 3);
		switch( $Code )
		{
			case '235':
				return true;
				break;
			case '535':
				return false;
				break;
			default:
				throw new Phools_Net_Smtp_Exception('Unhandled response-code "'.$Code.'"');
		}

		return false;
	}

	/**
	 *
	 * @var Phools_Net_Smtp_Client_Interface
	 */
	private $Client = null;

	/**
	 * Bind client to use during this authentication-session.
	 *
	 * @param Phools_Net_Smtp_Client_Interface $Client
	 *
	 * @return Phools_Net_Smtp_Extension_SaslAuthAfterEhlo
	 */
	protected function bindClient(Phools_Net_Smtp_Client_Interface $Client)
	{
		if ( $this->Client )
		{
			throw new Phools_Net_Smtp_Exception('Client already bound. Maybe we are stuck in a loop here.');
		}

		$this->Client = $Client;

		return $this;
	}

	/**
	 *
	 * @param Phools_Net_Smtp_Client_Interface $Client
	 */
	protected function getClient()
	{
		if ( ! $this->Client )
		{
			throw new Phools_Net_Smtp_Exception('No client bound.');
		}

		return $this->Client;
	}

	/**
	 *
	 * @return Phools_Net_Smtp_Extension_SaslAuthAfterEhlo
	 */
	protected function releaseClient()
	{
		$this->Client = null;

		return $this;
	}

	/**
	 *
	 * @var Phools_Net_Sasl_Mechanism_Interface
	 */
	private $Mechanism = null;

	/**
	 *
	 * @param Phools_Net_Sasl_Mechanism_Interface $Mechanisms[]
	 */
	public function setMechanism(Phools_Net_Sasl_Mechanism_Interface $Mechanism)
	{
		$this->Mechanism = $Mechanism;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Sasl_Mechanism_Interface
	 */
	protected function getMechanism()
	{
		return $this->Mechanism;
	}

}
