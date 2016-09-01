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
interface Phools_Net_Smtp_Writer_Interface
extends
	Phools_Net_Mime_Writer_Interface
{

	/**
	 * Write an group-address-field, e.g.
	 *
	 * Group: "Foo Bar" <foo.bar@example.com>.
	 *
	 * @param string $Name
	 * @param array $Mailboxes
	 */
	public function writeAddressGroup($Name, array $Mailboxes);

	/**
	 *
	 * @param string $Address
	 * @param string $Name
	 */
	public function writeAddressMailbox($Address, $Name = '');

	/**
	 *
	 * @param string $Name
	 * @param array $Addresses
	 *
	 * @return string
	 */
	public function writeHeaderAddressList($Name, array $Addresses);

	/**
	 *
	 * @param string $Name
	 * @param Phools_Net_Smtp_Address_Mailbox $Mailbox
	 *
	 * @return string
	 */
	public function writeHeaderMailbox($Name, Phools_Net_Smtp_Address_Mailbox $Mailbox);

	/**
	 *
	 * @param string $Name
	 * @param array $Mailboxes
	 *
	 * @return string
	 */
	public function writeHeaderMailboxList($Name, array $Mailboxes);

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 *
	 * @return string
	 */
	public function writeHeaderUnstructured($Name, $Value);

	/**
	 *
	 * @return string
	 */
	public function writeText($Text);

}
