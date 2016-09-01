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
abstract class Phools_Net_Smtp_Writer_Abstract
implements Phools_Net_Smtp_Writer_Interface
{

	const CRLF = "\r\n";
	const CR = "\r";
	const LF = "\n";

	/**
	 *
	 * @param string $String
	 * @return string
	 */
	protected function foldWhitespace($String)
	{
		// @todo implement whitespace folding
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Writer_Interface::writeAddressGroup()
	 */
	public function writeAddressGroup($Name, array $Mailboxes)
	{
		$this->append($Name . ': ');

		$First = true;
		while( $Mailbox = array_shift($Mailboxes) )
		{
			if ( ! $First ) {
				$this->append(', ');
			} else {
				$First = false;
			}

			$Mailbox->write($this);
		}

		$this->append('; ');

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Writer_Interface::writeAddressMailbox()
	 */
	public function writeAddressMailbox($Address, $Name = '')
	{
		if ( ! empty($Name) )
		{
			$Name = str_replace('"', '\"', $Name);
			$this->append('"'.$Name.'" <'.$Address.'>');
		}
		else
		{
			$this->append($Address);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Writer_Interface::formatHeaderAddressList()
	 */
	public function writeHeaderAddressList($Name, array $Addresses)
	{
		$this->append($Name . ': ');

		$First = true;
		while( $Address = array_shift($Addresses) )
		{
			if ( ! $First ) {
				$this->append(', ');
			} else {
				$First = false;
			}

			$Address->write($this);
		}

		$this->append(self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Writer_Interface::formatHeaderMailbox()
	 */
	public function writeHeaderMailbox($Name, Phools_Net_Smtp_Address_Mailbox $Mailbox)
	{
		$this->append($Name . ': ');

		$Mailbox->write($this);

		$this->append(self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Writer_Interface::formatHeaderMailboxList()
	 */
	public function writeHeaderMailboxList($Name, array $Mailboxes)
	{
		$this->append($Name . ': ');

		$First = true;
		while( $Mailbox = array_shift($Mailboxes) )
		{
			if ( ! $First ) {
				$this->append(', ');
			} else {
				$First = false;
			}

			$Mailbox->write($this);
		}

		$this->append(self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Writer_Interface::formatHeaderMimeType()
	 */
	public function writeHeaderMimeType($Name, $Type, $Subtype, array $Params)
	{
		$this->append($Name . ': ');
		$this->append($Type . '/' . $Subtype);

		if ( ! empty($Params) )
		{
			$this->append('; ');

			$First = true;
			foreach( $Params as $Name => $Value )
			{
				if ( ! $First ) {
					$this->append(', ');
				} else {
					$First = false;
				}

				$this->append($Name . '=' . $Value);
			}

		}

		$this->append(self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Writer_Interface::formatHeaderUnstructured()
	 */
	public function writeHeaderUnstructured($Name, $Value)
	{
		$this->append($Name . ': ' . $Value . self::CRLF);

		return $this;
	}

	/**
	 *
	 * @param string $Text
	 */
	public function writeText($Text)
	{
		$Text = str_replace(self::CRLF, self::LF, $Text);
		$Text = str_replace(self::CR, self::LF, $Text);

		$this->append(self::CRLF . $Text);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Writer_Interface::writeMimeVersion()
	 */
	public function writeMimeVersion($Version)
	{
		$this->append('MIME-Version: ' . $Version . self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Writer_Interface::writeMimeType()
	 */
	public function writeMimeType($MimeType, array $Params = array())
	{
		$this->append('Content-Type: ' . $MimeType);

		if ( 0 < count($Params) )
		{
			foreach( $Params as $Name => $Value )
			{
				$this->append('; ' . $Name . '=' . $Value);
			}
		}

		$this->append(self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Writer_Interface::writeMimeContentDisposition()
	 */
	public function writeMimeContentDisposition($Type, array $Params = array())
	{
		$this->append('Content-Disposition: ' . $Type);

		if ( 0 < count($Params) )
		{
			foreach( $Params as $Name => $Value )
			{
				$this->append('; ' . $Name . '=' . $Value);
			}
		}

		$this->append(self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Writer_Interface::writeMimeBoundaryStart()
	 */
	public function writeMimeBoundaryStart($Boundary)
	{
		$this->append(self::CRLF . self::CRLF . '--' . $Boundary . self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Writer_Interface::writeMimeBoundaryEnd()
	 */
	public function writeMimeBoundaryEnd($Boundary)
	{
		$this->append(self::CRLF . '--' . $Boundary . '--' . self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Writer_Interface::writeMimeTransferEncoding()
	 */
	public function writeMimeTransferEncoding($Name)
	{
		$this->append('Content-Transfer-Encoding: ' . $Name . self::CRLF);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Writer_Interface::writeMimeContent()
	 */
	public function writeMimeContent($Content)
	{
		$this->append($Content);

		return $this;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $LineLength = 78;

	/**
	 *
	 *
	 * @param int $LineLength
	 * @return Phools_Net_Smtp_Writer_Abstract
	 */
	public function setLineLength($LineLength)
	{
		$this->LineLength = (int) $LineLength;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getLineLength()
	{
		return $this->LineLength;
	}

}
