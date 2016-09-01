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
 * Implementing RFC 5322, Section 3.6.2, header mailbox
 *
 * @see http://tools.ietf.org/html/rfc5322#section-3.6.2
 */
class Phools_Net_Smtp_Header_Mailbox
extends Phools_Net_Smtp_Header_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param Phools_Net_Smtp_Address_Mailbox $Mailbox
	 */
	public function __construct($Name, Phools_Net_Smtp_Address_Mailbox $Mailbox)
	{
		parent::__construct($Name);

		$this->setMailbox($Mailbox);
	}

	public function __destruct()
	{
		$this->Mailbox = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Header_Interface::format()
	 */
	public function write(Phools_Net_Smtp_Writer_Interface $Formatter)
	{
		return $Formatter->writeHeaderMailbox(
			$this->getName(),
			$this->getMailbox());
	}

	/**
	 *
	 * @var Phools_Net_Smtp_Address_Mailbox
	 */
	private $Mailbox = null;

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Mailbox $Mailbox
	 */
	public function setMailbox(Phools_Net_Smtp_Address_Mailbox $Mailbox)
	{
		$this->Mailbox = $Mailbox;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Smtp_Address_Mailbox
	 */
	protected function getMailbox()
	{
		return $this->Mailbox;
	}

}
