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
 * Implementing RFC5322, Section 3.4, mailbox.
 *
 * A mailbox-address consists of 2 parts, the required address where the
 * mailbox is reachable and optionally a human-friendly name assigned with
 * the mailbox.
 *
 * @see http://tools.ietf.org/html/rfc5322#section-3.4
 */
class Phools_Net_Smtp_Address_Mailbox
implements Phools_Net_Smtp_Address_Interface
{

	/**
	 * Construct a new mailbox-address.
	 *
	 * @param string $Address
	 * @param string $Name
	 */
	public function __construct($Address, $Name = '')
	{
		$this
			->setAddress($Address)
			->setName($Name);
	}

	/**
	 *
	 *
	 */
	public function __destruct()
	{
		$this->Name = null;
		$this->Address = null;
	}

	public function __toString()
	{
		$String = '';

		if ( $this->getName() )
		{
			$String .= $this->getName() . ' ';
		}

		$String .= '<' . $this->getAddress() . '>';

		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Address_Interface::write()
	 */
	public function write(Phools_Stream_Output_Interface &$Output)
	{
		$EncodedWord = new Phools_Stream_Output_EncodedWord($Output);

		$Name = $this->getName();
		if ( $Name )
		{
			$Name = str_replace('"', '\"', $Name);

			$EncodedWord->write('"'.$Name.'"');

			$EncodedWord->write(' <'.$this->getAddress().'>');
		}
		else
		{
			$EncodedWord->write($this->getAddress());
		}

		$EncodedWord->flush();

		return $this;
	}

	/**
	 * Keep the mailbox's address.
	 *
	 * @var string
	 */
	private $Address = '';

	/**
	 * Set this amilbox's address.
	 *
	 * @param string $Address
	 *
	 * @return Phools_Net_Smtp_Address_Mailbox
	 */
	public function setAddress($Address)
	{
		assert( is_string($Address) );

		$this->Address = $Address;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getAddress()
	{
		return $this->Address;
	}

	/**
	 * Keep the mailbox' owner's name.
	 *
	 * @var string
	 */
	private $Name = '';

	/**
	 * Set the mailbox' owner's name.
	 *
	 * @param string $Name
	 *
	 * @return Phools_Net_Smtp_Address_Mailbox
	 */
	public function setName($Name)
	{
		assert( is_string($Name) );

		$this->Name = $Name;

		return $this;
	}

	/**
	 * Get the mailbox' owner's name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->Name;
	}

}
