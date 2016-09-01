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
class Phools_Net_Smtp_Message_Mime
extends Phools_Net_Smtp_Message_Abstract
{

	/**
	 *
	 * @param string $MimeVersion
	 */
	public function __construct($MimeVersion = '1.0')
	{
		$this->setMimeVersion($MimeVersion);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->ContentType = null;
		$this->MimeVersion = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::read()
	 */
	public function read(Phools_Stream_Input_Interface $InputStream)
	{
		return parent::read($InputStream);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::write()
	 */
	public function write(Phools_Stream_Output_Interface $Output)
	{
		$MimeVersion = $this->getMimeVersion();
		if ( $MimeVersion )
		{
			$Output->write('MIME-Version: ');

			$Output->write($MimeVersion);
		}

		$Output->write(self::CRLF);

		$ContentType = $this->getContentType();
		if ( $ContentType )
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());

			$FoldingWhitespace->write('Content-Type: ');
			$FoldingWhitespace->write($ContentType->getMimeType());
			$FoldingWhitespace->flush();
		}

		$Output->write(self::CRLF);

		$Date = $this->getDate();
		if ( $Date )
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());
			$FoldingWhitespace
				->write('Date: ' . $this->getDate()->format('r') . self::CRLF);
			$FoldingWhitespace->flush();
		}
		else
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());
			$FoldingWhitespace
				->write('Date: ' . date('r') . self::CRLF);
			$FoldingWhitespace->flush();
		}

		$Sender = $this->getSender();
		if ( $Sender )
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());
			$FoldingWhitespace->write('Sender: ');
			$Sender->write($FoldingWhitespace);
			$FoldingWhitespace->flush();
		}

		$Output->write(self::CRLF);

		$Authors = $this->getAuthors();
		if ( ! empty($Authors) )
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());
			$FoldingWhitespace->write('From: ');

			$First = true;
			while ( $Address = array_shift($Authors) )
			{
				$First ? $First = false : $FoldingWhitespace->write(', ');
				$Address->write($FoldingWhitespace);
			}

			$FoldingWhitespace->flush();
		}

		$Output->write(self::CRLF);

		$ReplyTos = $this->getReplyTos();
		if ( ! empty($ReplyTos) )
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());
			$FoldingWhitespace->write('Reply-To: ');

			$First = true;
			while( $Address = array_shift($ReplyTos) )
			{
				$First ? $First = false : $FoldingWhitespace->write(', ');
				$Address->write($FoldingWhitespace);
			}

			$FoldingWhitespace->flush();
		}

		$Output->write(self::CRLF);

		$Recipients = $this->getRecipients();
		if ( ! empty($Recipients) )
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());
			$FoldingWhitespace->write('To: ');

			$First = true;
			while( $Address = array_shift($Recipients) )
			{
				$First ? $First = false : $FoldingWhitespace->write(', ');
				$Address->write($FoldingWhitespace);
			}

			$FoldingWhitespace->flush();
		}

		$Output->write(self::CRLF);

		$CarbonCopies = $this->getCcs();
		if ( ! empty($CarbonCopies) )
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());
			$Output->write('Cc: ');

			$First = true;
			while( $Address = array_shift($CarbonCopies) )
			{
				$First ? $First = false : $FoldingWhitespace->write(', ');
				$Address->write($FoldingWhitespace);
			}

			$FoldingWhitespace->flush();
		}

		$Output->write(self::CRLF);

		$BlindCarbonCopies = $this->getBccs();
		if ( ! empty($BlindCarbonCopies) )
		{
			$FoldingWhitespace = new Phools_Stream_Output_FoldingWhitespace($Output, $this->getLineLength());
			$Output->write('Bcc: ');

			$First = true;
			while( $Address = array_shift($BlindCarbonCopies) )
			{
				$First ? $First = false : $FoldingWhitespace->write(', ');
				$Address->write($FoldingWhitespace);
			}
			$FoldingWhitespace->flush();

		}

		$Output->write(self::CRLF);

		$Subject = $this->getSubject();
		if ( $Subject )
		{
			$Output->write('Subject: ');
			$EncodedWord = new Phools_Stream_Output_EncodedWord($Output);
			$EncodedWord->write($Subject)->flush();
			$Output->write(self::CRLF);
		}

		$Output->write(self::CRLF);

		$Text = $this->getText();
		if ( $Text )
		{
			$QPEncoding = new Phools_Stream_Output_QuotedPrintable($Output);
			$QPEncoding->write($this->getText());

			$QPEncoding->flush();
		}

		$Output->write(self::CRLF);

		$MimeParts = $this->getMimeParts();
		while( $MimePart = array_shift($MimeParts) )
		{
			$Output->write( $this->getBoundary() . self::CRLF );
			$MimePart->write($Output);

			$Output->flush();
		}

		$Output->write(self::CRLF);

		$Output->write( '--' . $this->getBoundary());

		$Output->write(self::CRLF);

		return $this;
	}

	public function getAllHeaders()
	{
		$MimeVersion = $this->getMimeVersion();
		$Headers[] = new Phools_Net_Smtp_Header_Mime_Version($MimeVersion);
	}

	/**
	 * Holds the MIME-version of this message.
	 *
	 * @var string
	 */
	protected $MimeVersion = '';

	/**
	 * Set the mime-version for this message.
	 *
	 * @param string $MimeVersion
	 * @return Phools_Net_Smtp_Message
	 */
	public function setMimeVersion($MimeVersion)
	{
		$this->MimeVersion = (string) $MimeVersion;

		return $this;
	}

	/**
	 * Get this message's MIME-version.
	 *
	 * @return string
	 */
	public function getMimeVersion()
	{
		return $this->MimeVersion;
	}

	/**
	 * Hold this message's content-type.
	 *
	 * @var Phools_Net_Mime_Type_Interface
	 */
	protected $ContentType = null;

	/**
	 * Set the content-type of this message.
	 *
	 * @param Phools_Net_Mime_Type_Interface $ContentType
	 * @return Phools_Net_Smtp_Message_Mime_Abstract
	 */
	public function setContentType(Phools_Net_Mime_Type_Interface $ContentType)
	{
		$this->ContentType = $ContentType;
		return $this;
	}

	/**
	 * Get the content-type of this message.
	 *
	 * @return Phools_Net_Mime_Type_Interface
	 */
	public function getContentType()
	{
		return $this->ContentType;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Boundary = 'PHOOLS_MESSAGE_BOUNDARY';

	/**
	 *
	 *
	 * @param string $Boundary
	 * @return Phools_Net_Smtp_Message
	 */
	public function setBoundary($Boundary)
	{
		$this->Boundary = (string) $Boundary;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getBoundary()
	{
		return $this->Boundary;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $ContentId = '';

	/**
	 *
	 *
	 * @param string $ContentId
	 * @return Phools_Net_Smtp_Message_Mime
	 */
	public function setContentId($ContentId = '')
	{
		$this->ContentId = (string) $ContentId;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getContentId()
	{
		return $this->ContentId;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $ContentDescription = '';

	/**
	 *
	 *
	 * @param string $ContentDescription
	 * @return Phools_Net_Smtp_Message_Mime
	 */
	public function setContentDescription($ContentDescription = '')
	{
		$this->ContentDescription = (string) $ContentDescription;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getContentDescription()
	{
		return $this->ContentDescription;
	}

	/**
	 *
	 * @var array
	 */
	private $MimeParts = array();

	/**
	 *
	 * @param Phools_Net_Mime_Part_Interface $MimePart
	 *
	 * @return Phools_Net_Smtp_Message_Mime
	 */
	public function appendMimePart(Phools_Net_Mime_Part_Interface $MimePart)
	{
		array_push($this->MimeParts, $MimePart);

		return $this;

	}

	/**
	 *
	 * @param Phools_Net_Mime_Part_Interface $MimePart
	 *
	 * @return Phools_Net_Smtp_Message_Mime
	 */
	public function prependMimePart(Phools_Net_Mime_Part_Interface $MimePart)
	{
		array_unshift($this->MimeParts, $MimePart);

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getMimeParts()
	{
		return $this->MimeParts;
	}

}
