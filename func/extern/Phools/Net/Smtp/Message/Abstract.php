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
abstract class Phools_Net_Smtp_Message_Abstract
implements Phools_Net_Smtp_Message_Interface
{

	/**
	 *
	 * @var string
	 */
	const CRLF = "\r\n";

	/**
	 * Garbage collect.
	 *
	 */
	public function __destruct()
	{
		$this->Text = null;

		$this->Headers = null;

		$this->ReplyTos = null;
		$this->Sender = null;
		$this->Authors = null;

		$this->Bccs = null;
		$this->Ccs = null;
		$this->Recipients = null;

		$this->Keywords = null;
		$this->Comments = null;
		$this->Subject = null;

		$this->Keywords = null;
		$this->Comments = null;
		$this->Subject = null;

		$this->Date = null;
	}

	/**
	 *
	 * @var array
	 */
	private $Headers = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::addHeader()
	 */
	public function addHeader(Phools_Net_Smtp_Header_Interface $Header)
	{
		$HeaderName = $Header->getName();

		switch( $HeaderName )
		{
			// origination date
			case 'DATE':

			// originator fields
			case 'FROM':
			case 'SENDER':
			case 'REPLY-TO':

			// destination address fields
			case 'TO':
			case 'CC':
			case 'BCC':

			// identification fields
			case 'MESSAGE-ID':
			case 'IN-REPLY-TO':
			case 'REFERENCES':

			// informational fields
			case 'SUBJECT':
			case 'COMMENTS':
			case 'KEYWORDS':

			// resent fields
			case 'RESENT-DATE':
			case 'RESENT-FROM':
			case 'RESENT-SENDER':
			case 'RESENT-TO':
			case 'RESENT-CC':
			case 'RESENT-BCC':
			case 'RESENT-MSG-ID':

			// trace fields
			case 'RETURN-PATH':
			case 'RECEIVED':
				break;
		}

		array_push($this->Headers[], $Header);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::setHeader()
	 */
	public function setHeader(Phools_Net_Smtp_Header_Interface $Header)
	{
		foreach( $this->Headers as $Key => $ExistingHeader )
		{
			if ( $ExistingHeader->getName() == $Header->getName() )
			{
				unset($this->Headers[$Key]);
			}
		}

		$this->Headers[] = $Header;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::getHeader()
	 */
	public function getHeader($Name)
	{
		foreach( $this->Headers as $Header )
		{
			if ( $Name == $Header->getName() )
			{
				return $Header;
			}
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::getHeaders()
	 */
	public function getHeaders($Name)
	{
		$Headers = array();

		foreach( $this->Headers as $Header )
		{
			if ( $Name == $Header->getName() )
			{
				$Headers[] = $Header;
			}
		}

		return $Headers;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::getAllHeaders()
	 */
	public function getAllHeaders()
	{
		$Headers = array();

		$Headers[] = new Phools_Net_Smtp_Header_Unstructured('Date', date('r'));

		$Authors = $this->getAuthors();
		if ( 1 < count($Authors) )
		{
			$Sender = $Authors[0];
			$this->setSender($Sender);
		}

		$Headers[] = new Phools_Net_Smtp_Header_MailboxList('From', $Authors);

		$Sender = $this->getSender();
		if ( $Sender )
		{
			$Headers[] = new Phools_Net_Smtp_Header_Mailbox('Sender', $Sender);
		}

		$ReplyTos = $this->getReplyTos();
		if ( ! empty($ReplyTos) )
		{
			$Headers[] = new Phools_Net_Smtp_Header_MailboxList('Reply-To', $ReplyTos);
		}

		$Recipients = $this->getRecipients();
		if ( ! empty($Recipients) )
		{
			$Headers[] = new Phools_Net_Smtp_Header_AddressList('To', $Recipients);
		}

		$Ccs = $this->getCcs();
		if ( ! empty($Ccs) )
		{
			$Headers[] = new Phools_Net_Smtp_Header_AddressList('Cc', $Ccs);
		}

		$Bccs = $this->getBccs();
		if ( ! empty($Bccs) )
		{
			$Headers[] = new Phools_Net_Smtp_Header_AddressList('Bcc', $Bccs);
		}

		$MessageId = $this->getMessageId();
		if ( $MessageId )
		{
			$Headers[] = new Phools_Net_Smtp_Header_Unstructured('Message-ID', $MessageId);
		}

		$InReplyTo = $this->getInReplyTo();
		if ( $InReplyTo )
		{
			$Headers[] = new Phools_Net_Smtp_Header_Unstructured('In-Reply-To', $InReplyTo);
		}

		$Reference = $this->getReference();
		if ( $Reference )
		{
			$Headers[] = new Phools_Net_Smtp_Header_Unstructured('References', $Reference);
		}

		$Subject = $this->getSubject();
		if ( $Subject )
		{
			$Headers[] = new Phools_Net_Smtp_Header_Unstructured('Subject', $Subject);
		}

		$Comments = $this->getComments();
		while ( $Comment = array_shift($Comments) )
		{
			$Headers[] = new Phools_Net_Smtp_Header_Unstructured('Comments', $Comment);
		}

		$Keywords = $this->getKeywords();
		while ( $Keyword = array_shift($Keywords) )
		{
			$Headers[] = new Phools_Net_Smtp_Header_Unstructured('Keywords', $Keyword);
		}

		$Headers = array_merge($Headers, $this->Headers);

		return $Headers;
	}

	/**
	 *
	 *
	 * @var DateTime
	 */
	protected $Date = null;

	/**
	 *
	 *
	 * @param DateTime $Date
	 * @return Phools_Net_Smtp_Message_Abstract
	 */
	public function setDate(DateTime $Date)
	{
		$this->Date = $Date;
		return $this;
	}

	/**
	 *
	 * @return DateTime
	 */
	public function getDate()
	{
		return $this->Date;
	}

	/**
	 *
	 * @var Phools_Net_Smtp_Address_Mailbox
	 */
	private $Sender = null;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::setSender()
	 */
	public function setSender(Phools_Net_Smtp_Address_Mailbox $Sender)
	{
		$this->Sender = $Sender;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getSender()
	 */
	public function getSender()
	{
		if ( ! $this->Sender )
		{
			if ( ! empty($this->Authors) )
			{
				$Sender = $this->Authors[0];
				$this->setSender($Sender);
			}
		}

		return $this->Sender;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Smtp_Address_Mailbox
	 */
	private $Authors = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::addAuthor()
	 */
	public function addAuthor(Phools_Net_Smtp_Address_Mailbox $Author)
	{
		/*
		 * @see RFC 5322, sec 3.6.2. Originator Fields
		 *
		 * If the from field contains more than one mailbox specification in
		 * the mailbox-list, then the sender field, containing the field name
		 * "Sender" and a single mailbox specification, MUST appear in the
		 * message.
		 */
		if ( ! empty($this->Authors) )
		{
			if ( ! $this->getSender() )
			{
				$this->setSender($this->Authors[0]);
			}
		}

		$this->Authors[] = $Author;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getAuthors()
	 */
	public function getAuthors()
	{
		return $this->Authors;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Smtp_Address_Interface
	 */
	private $ReplyTos = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::addReplyTo()
	 */
	public function addReplyTo(Phools_Net_Smtp_Address_Interface $ReplyTo)
	{
		$this->ReplyTos[] = $ReplyTo;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getReplyTos()
	 */
	public function getReplyTos()
	{
		return $this->ReplyTos;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Smtp_Address_Interface
	 */
	private $Recipients = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::addRecipient()
	 */
	public function addRecipient(Phools_Net_Smtp_Address_Interface $Recipient)
	{
		$this->Recipients[] = $Recipient;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getRecipients()
	 */
	public function getRecipients()
	{
		return $this->Recipients;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Smtp_Address_Interface
	 */
	private $Ccs = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::addCc()
	 */
	public function addCc(Phools_Net_Smtp_Address_Interface $Cc)
	{
		$this->Ccs[] = $Cc;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getCcs()
	 */
	public function getCcs()
	{
		return $this->Ccs;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Smtp_Address_Interface
	 */
	private $Bccs = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::addBcc()
	 */
	public function addBcc(Phools_Net_Smtp_Address_Interface $Bcc)
	{
		$this->Bccs[] = $Bcc;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getBccs()
	 */
	public function getBccs()
	{
		return $this->Bccs;
	}

	/**
	 *
	 * @var string
	 */
	private $MessageId = '';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::setMessageId()
	 */
	public function setMessageId($MessageId)
	{
		$this->MessageId = (string) $MessageId;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getMessageId()
	 */
	public function getMessageId()
	{
		return $this->MessageId;
	}

	/**
	 *
	 * @var string
	 */
	private $InReplyTos = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::addInReplyTo()
	 */
	public function addInReplyTo($InReplyTo)
	{
		$this->InReplyTos[] = (string) $InReplyTo;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getInReplyTo()
	 */
	public function getInReplyTos()
	{
		return $this->InReplyTos;
	}

	/**
	 *
	 * @var string
	 */
	private $References = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::addReference()
	 */
	public function addReference($Reference)
	{
		$this->References[] = (string) $Reference;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getReferences()
	 */
	public function getReferences()
	{
		return $this->References;
	}

	/**
	 *
	 * @var string
	 */
	private $Subject = '';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::setSubject()
	 */
	public function setSubject($Subject)
	{
		$this->Subject = (string) $Subject;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Simple_Interface::getSubject()
	 */
	public function getSubject()
	{
		return $this->Subject;
	}

	/**
	 *
	 * @var array
	 */
	private $Comments = array();

	/**
	 *
	 * @param string $Comment
	 */
	public function addComment($Comment)
	{
		$this->Comments[] = (string) $Comment;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getComments()
	{
		return $this->Comments;
	}

	/**
	 *
	 * @var array
	 */
	private $Keywords = array();

	/**
	 *
	 * @param string $Keyword
	 */
	public function addKeyword($Keyword)
	{
		$this->Keywords[] = (string) $Keyword;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getKeywords()
	{
		return $this->Keywords;
	}

	/**
	 *
	 * @var string
	 */
	private $Text = null;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::setText()
	 */
	public function setText($Text = '')
	{
		$this->Text = (string) $Text;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Message_Interface::getText()
	 */
	public function getText()
	{
		return $this->Text;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $Priority = Phools_Net_Smtp_Priority::NORMAL;

	/**
	 *
	 *
	 * @param int $Priority
	 *
	 * @return Phools_Net_Smtp_Message_Abstract
	 */
	public function setPriority($Priority)
	{
		assert( is_int($Priority) );
		assert( 0 < $Priority );
		assert( 6 > $Priority );

		$this->Priority = $Priority;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getPriority()
	{
		return $this->Priority;
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
	 * @return Phools_Net_Smtp_Message_Abstract
	 */
	public function setLineLength(int $LineLength)
	{
		assert( is_int($LineLength) );
		assert( 0 < $LineLength );

		$this->LineLength = $LineLength;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function getLineLength()
	{
		return $this->LineLength;
	}

}
