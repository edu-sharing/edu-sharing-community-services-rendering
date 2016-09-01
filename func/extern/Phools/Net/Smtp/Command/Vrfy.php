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
class Phools_Net_Smtp_Command_Vrfy
extends Phools_Net_Smtp_Command_Abstract
{

	/**
	 *
	 * @param string $Identity
	 */
	public function __construct($Identity)
	{
		$this->setIdentity($Identity);
	}

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
		$Output->write('VRFY ' . $this->getIdentity());
		$Output->write(Phools_Net_Smtp_Command_Interface::CRLF);

		return $this;
	}

	/**
	 * Hold the identity to verify.
	 *
	 * @var string
	 */
	private $Identity = '';

	/**
	 * Set the identity to verify.
	 *
	 * @param string $Identity
	 *
	 * @return Phools_Net_Smtp_Command_Rcpt
	 */
	public function setIdentity($Identity)
	{
		assert( is_string($Identity) );

		$this->Identity = $Identity;
		return $this;
	}

	/**
	 * Get the identity to verify.
	 *
	 * @return string
	 */
	protected function getIdentity()
	{
		return $this->Identity;
	}

}
