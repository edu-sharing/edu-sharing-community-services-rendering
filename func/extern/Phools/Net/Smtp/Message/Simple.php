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
class Phools_Net_Smtp_Message_Simple
extends Phools_Net_Smtp_Message_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::read()
	 */
	public function read(Phools_Stream_Input_Interface $InputStream)
	{
		$Buffer = new Phools_Stream_Input_Buffer($InputStream);
		$Lines = new Phools_Stream_Input_Line($Buffer);

		$HeaderName = '';
		switch( $HeaderName )
		{
			case 'DATE':
				$Header = new Phools_Net_Smtp_Header_Date($HeaderName);
				$Header->read($InputStream);

				$this->setDate($Header);

				break;

			case 'FROM':
				$Header = new Phools_Net_Smtp_Header_AddressList($HeaderName);
				$Header->read($InputStream);

				$this->setFrom($Header);

				break;

			case 'SENDER':
				$Header = new Phools_Net_Smtp_Header_Mailbox($HeaderName);
				$Header->read($InputStream);

				$this->setSender($Header);

				break;

			default:
				$Header = new Phools_Net_Smtp_Header_Unstructured($HeaderName);
				$Header->read($InputStream);

				$this->addHeader($Header);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::write()
	 */
	public function write(Phools_Stream_Output_Interface $Output)
	{
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
var_dump($Date);
// exit();
			$FoldingWhitespace->write('Date: ' . date('r') . self::CRLF);
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

		return $this;
	}

}

