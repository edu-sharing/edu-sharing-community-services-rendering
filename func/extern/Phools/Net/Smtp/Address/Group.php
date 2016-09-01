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
 * Implementing RFC5322, Section 3.4, group
 *
 * @see http://tools.ietf.org/html/rfc5322#section-3.4
 */
class Phools_Net_Smtp_Address_Group
implements Phools_Net_Smtp_Address_Interface
{

	/**
	 *
	 * @param string $Name
	 * @param array $Mailboxes
	 */
	public function __construct($Name, array $Mailboxes = array())
	{
		$this->setName($Name);

		foreach ( $Mailboxes as $Mailbox )
		{
			$this->addMailbox($Mailbox);
		}
	}

	/**
	 * Free mailboxes
	 *
	 */
	public function __destruct()
	{
		$this->Mailboxes = null;
	}

	public function __toString()
	{
		$String = $this->getName() . ':';

		$First = true;
		foreach( $this->getMailboxes() as $Mailbox )
		{
			if ( ! $First ) {
				$EncodedWord->write(', ');
			} else {
				$First = false;
			}

			$String .= $Mailbox;
		}

		$String = ';';

		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Address_Interface::write()
	 */
	public function write(Phools_Stream_Output_Interface &$Output)
	{
		$EncodedWord = new Phools_Stream_Output_EncodedWord($Output);
		$EncodedWord->write($this->getName() . ': ');

		$First = true;
		foreach( $this->getMailboxes() as $Mailbox )
		{
			if ( ! $First ) {
				$EncodedWord->write(', ');
			} else {
				$First = false;
			}

			$Mailbox->write($Output);
		}

		$EncodedWord->write(';');
		$EncodedWord->flush();

		return $this;
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

	/**
	 * Keep this mailbox-group name.
	 *
	 * @var string
	 */
	private $Name = '';

	/**
	 * Set the mailbox-group's name.
	 *
	 * @param string $Name
	 *
	 * @return Phools_Net_Smtp_Address_Group
	 */
	public function setName($Name)
	{
		assert( is_string($Name) );

		if ( empty($Name) )
		{
			throw new Phools_Net_Smtp_Exception('Group-name cannot be empty.');
		}

		$this->Name = (string) $Name;

		return $this;
	}

	/**
	 * Get the mailbox-group's name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->Name;
	}

}
