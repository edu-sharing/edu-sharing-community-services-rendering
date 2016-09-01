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
abstract class Phools_Net_Smtp_Client_Connection_Abstract
implements Phools_Net_Smtp_Client_Interface
{

	const CRLF = "\r\n";

	/**
	 *
	 * @param Phools_Net_Connection_Interface $Connection
	 * @param string $Identity
	 */
	public function __construct(
		Phools_Net_Connection_Interface $Connection,
		$Identity = 'localhost')
	{
		$this
			->setConnection($Connection)
			->setIdentity($Identity);
	}

	/**
	 * Terminate current session if required.
	 *
	 */
	public function __destruct()
	{
		$this->Identity = null;

		if ( $this->getConnection()->isEstablished() )
		{
			$this->terminate();
		}
	}

	public function isInitialized()
	{
		if ( $this->getConnection()->isEstablished() )
		{
			return true;
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Client_Interface::writeLine()
	 */
	public function writeLine($Data)
	{
		$Data .= self::CRLF;

		// repeat until all $Data sent
		$Connection = $this->getConnection();
		if ( ! $Connection )
		{
			throw new Phools_Net_Smtp_Exception('No connection initialized.');
		}

		$BytesTotal = 0;
		do {
			$BytesWritten = $Connection->write($Data);
			if ( false === $BytesWritten )
			{
				throw new Phools_Net_Smtp_Exception('Error writing line.');
			}

			$BytesTotal += $BytesWritten;

			$Data = substr($Data, $BytesWritten);
		}
		while ( strlen($Data) );

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Client_Interface::readLine()
	 */
	public function readLine()
	{
		$Line = '';

		// read until CRLF appears
		$Connection = $this->getConnection();
		if ( ! $Connection )
		{
			throw new Phools_Net_Smtp_Exception('No connection initialized.');
		}

		do
		{
			$Char = $Connection->read(1);
			if ( false === $Char )
			{
				throw new Phools_Net_Smtp_Exception('Error reading line.');
			}

			$Line .= $Char;
		}
		while ( self::CRLF != substr($Line, -2) );

		// strip CRLF
		$Line = substr($Line, 0, -2);

		return $Line;
	}

	/**
	 *
	 * @var Phools_Net_Connection_Interface
	 */
	private $Connection = null;

	/**
	 *
	 * @param Phools_Net_Connection_Interface $Connection
	 */
	public function setConnection(Phools_Net_Connection_Interface &$Connection)
	{
		$this->Connection = $Connection;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Connection_Interface
	 */
	protected function &getConnection()
	{
		return $this->Connection;
	}

	/**
	 * Hold the client's $Identity to use when initializing.
	 *
	 * @var string
	 */
	protected $Identity = '';

	/**
	 * Set this client's identity to provide when initializing.
	 *
	 * @param string $Identity
	 * @return Phools_Net_Smtp_Client_Connection_Abstract
	 */
	public function setIdentity($Identity)
	{
		$this->Identity = (string) $Identity;
		return $this;
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
