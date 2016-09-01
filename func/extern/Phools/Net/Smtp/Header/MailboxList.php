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
 * Implementing RFC 5322, Section 3.6.2, header mailbox-list
 *
 * @see http://tools.ietf.org/html/rfc5322#section-3.6.2
 */
class Phools_Net_Smtp_Header_MailboxList
extends Phools_Net_Smtp_Header_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param array $Mailboxes
	 */
	public function __construct($Name, array $Mailboxes = array())
	{
		parent::__construct($Name);

		foreach( $Mailboxes as $Mailbox )
		{
			$this->addMailbox($Mailbox);
		}
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Mailboxes = null;

		parent::__destruct();
	}

	public function write(Phools_Net_Smtp_Writer_Interface $Formatter)
	{
		return $Formatter->writeHeaderMailboxList($this->getName(), $this->getMailboxes());
	}

	/**
	 *
	 * @var array
	 */
	private $Mailboxes = array();

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Mailbox $Mailbox
	 */
	public function addMailbox(Phools_Net_Smtp_Address_Mailbox $Mailbox)
	{
		$this->Mailboxes[] = $Mailbox;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getMailboxes()
	{
		return $this->Mailboxes;
	}

}
