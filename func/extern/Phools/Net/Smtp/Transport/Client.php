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
 * Sends mail instantly by utilizing the $Client set.
 *
 *
 */
class Phools_Net_Smtp_Transport_Client
implements
	Phools_Net_Smtp_Transport_Interface
{

	/**
	 *
	 * @param Phools_Net_Smtp_Client_Interface $Client
	 */
	public function __construct(Phools_Net_Smtp_Client_Interface $Client)
	{
		$this->setClient($Client);
	}

	/**
	 *
	 *
	 */
	public function __destruct()
	{
		$Client = $this->getClient();
		if ( $Client->isInitialized() )
		{
			$Client->terminate();
		}

		$this->Client = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Transport_Interface::send()
	 */
	public function send(Phools_Net_Smtp_Message_Interface $Message, $From, array $To)
	{
		$Client = $this->getClient();
		if ( ! $Client->isInitialized() )
		{
			$Client->initialize();
		}

		ob_start();
		$Writer = new Phools_Net_Smtp_Writer_Echo();
		$Message->write($Writer);
		$Data = ob_get_contents();
		ob_end_clean();

		if ( ! $Client->send($Data, $From, $To) )
		{
			return false;
		}

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Smtp_Client_Interface
	 */
	protected $Client = null;

	/**
	 *
	 *
	 * @param Phools_Net_Smtp_Client_Interface $Client
	 * @return Phools_Net_Smtp_Transport_Client
	 */
	public function setClient(Phools_Net_Smtp_Client_Interface $Client)
	{
		$this->Client = $Client;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Smtp_Client_Interface
	 */
	protected function getClient()
	{
		return $this->Client;
	}

}
