<?php

/**
 *
 *
 */
class Phools_Net_Smtp_Parser_Rfc5322
extends Phools_Parser_BufferedParser
{

	/**
	 *
	 * @param Phools_Net_Smtp_Message_Interface $Message to parse into
	 */
	public function __construct(Phools_Net_Smtp_Message_Interface $Message)
	{
		$this->setMessage($Message);

		$this
			->register(new Phools_Parser_Grammar_Rfc5234())
			->register(new Phools_Parser_Grammar_Rfc5322())
//			->register(new Phools_Parser_Grammar_Rfc2046())
//			->register(new Phools_Parser_Grammar_Rfc2045())
		;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Interface::onStart()
	 */
	public function onStart($Name, Phools_Stream_Input_Buffer &$InputBuffer)
	{
// var_dump('START: "'.$Name.'"');
		switch( $Name )
		{
			case 'version':

			case 'resent-date':
			case 'orig-date':

			case 'year':
			case 'month':
			case 'day':
			case 'hour':
			case 'minute':
			case 'second':
			case 'zone':
			case 'display-name':
			case 'addr-spec':
			case 'unstructured':
			case 'msg-id':
				$this->flush();
				break;

			case 'sender':
			case 'mailbox':
				$this->display_name = '';
				$this->addr_spec = '';
				$this->mailbox = null;
				$this->flush();
				break;

			case 'group':
				$this->group = null;
				break;

			case 'from':
			case 'to':
			case 'cc':
			case 'bcc':
			case 'mailbox-list':
				$this->mailbox_list = array();
				break;

			case 'address-list':
				$this->address_list = array();
				break;

			case 'date':
				$this->year = '';
				$this->month = '';
				$this->day = '';
				break;

			case 'time':
				$this->hour = 0;
				$this->minute = 0;
				$this->second = 0;
				break;

			case 'body':
				$this->text = '';
				$this->flush();
				break;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Interface::onStop()
	 */
	public function onStop($Name, Phools_Stream_Input_Buffer &$InputBuffer)
	{
// var_dump('STOP: "'.$Name.'", "'.$this->getBuffer().'"');
		switch( $Name )
		{
			case 'version':
				$Version = $this->getBuffer();
				$this->getMessage()->setMimeVersion($Version);
				break;

			case 'date-time':
				$this->date = $this->getDateTime();
				$this->flush();
				break;

			case 'orig-date':
				$this->getMessage()->setDate($this->date);
				break;

			case 'year':
				$this->year = trim( $this->getBuffer() );
				$this->flush();
				break;

			case 'month':
				$this->month = trim( $this->getBuffer() );
				$this->flush();
				break;

			case 'day':
				$this->day = trim( $this->getBuffer() );
				$this->flush();
				break;

			case 'hour':
				$this->hour = trim( $this->getBuffer() );
				$this->flush();
				break;

			case 'minute':
				$this->minute = trim( $this->getBuffer() );
				$this->flush();
				break;

			case 'second':
				$this->second = trim( $this->getBuffer() );
				$this->flush();
				break;

			case 'zone':
				$this->timezone = trim( $this->getBuffer() );
				$this->flush();
				break;

			case 'resent-date':
				$this->getMessage()->setDate($this->date);
				break;

			case 'display-name':
				$this->display_name = trim( $this->getBuffer() );
				break;

			case 'addr-spec':
				$this->addr_spec = trim( $this->getBuffer() );
				break;

			case 'mailbox':
				$this->mailbox = new Phools_Net_Smtp_Address_Mailbox($this->addr_spec, $this->display_name);
				$this->mailbox_list[] = $this->mailbox;
				$this->address_list[] = $this->mailbox;
				break;

			case 'group':
				$this->group = new Phools_Net_Smtp_Address_Group($this->display_name, $this->mailbox_list);
				$this->address_list[] = $this->group;
				break;

			case 'from':
				foreach( $this->mailbox_list as $Mailbox )
					$this->getMessage()->addAuthor($Mailbox);
				break;

			case 'sender':
				$this->getMessage()->setSender($this->mailbox);
				break;

			case 'to':
				foreach( $this->mailbox_list as $Mailbox )
					$this->getMessage()->addRecipient($Mailbox);
				break;

			case 'cc':
				foreach( $this->mailbox_list as $Mailbox )
					$this->getMessage()->addCc($Mailbox);
				break;

			case 'bcc':
				foreach( $this->mailbox_list as $Mailbox )
					$this->getMessage()->addBcc($Mailbox);
				break;

			case 'reply-to':
				foreach( $this->address_list as $Address )
					$this->getMessage()->addReplyTo($Address);
				break;

			case 'subject':
				$this->getMessage()->setSubject($this->unstructured);
				break;

			case 'unstructured':
				$this->unstructured = trim( $this->getBuffer() );
				break;

			case 'msg-id':
				$this->msg_id = trim( $this->getBuffer() );
				break;

			case 'message-id':
				$this->getMessage()->setMessageId($this->msg_id);
				break;

			case 'text':
				$this->text .= $this->getBuffer();
				$this->flush();
				break;

			case 'body':
				$this->getMessage()->setText($this->text);
				$this->flush();
				break;

		}
	}

	/**
	 * Helper method to transform parsed date-time-data into a DateTime-object.
	 *
	 * @return DateTime
	 */
	protected function getDateTime()
	{
		$Month = 0;
		switch( $this->month )
		{
			case 'Jan':
				$Month = 1;
				break;
			case 'Feb':
				$Month = 2;
				break;
			case 'Mar':
				$Month = 3;
				break;
			case 'Apr':
				$Month = 4;
				break;
			case 'May':
				$Month = 5;
				break;
			case 'Jun':
				$Month = 6;
				break;
			case 'Jul':
				$Month = 7;
				break;
			case 'Aug':
				$Month = 8;
				break;
			case 'Sep':
				$Month = 9;
				break;
			case 'Oct':
				$Month = 10;
				break;
			case 'Nov':
				$Month = 11;
				break;
			case 'Dec':
				$Month = 12;
				break;
		}

		// 2008-07-01T22:35:17.03+08:00
		$DateTime = $this->year
		. '-' . sprintf('%02d', $Month)
		. '-' . sprintf('%02d', $this->day)
		. 'T' . sprintf('%02d', $this->hour)
		. ':' . sprintf('%02d', $this->minute)
		. ':' . sprintf('%02d', $this->second)
		. '.00'
		. $this->timezone;

		return new DateTime($DateTime);
	}

	/**
	 *
	 * @var Phools_Net_Smtp_Message_Interface
	 */
	private $Message = null;

	/**
	 *
	 * @param Phools_Net_Smtp_Message_Interface $Message
	 */
	protected function setMessage(Phools_Net_Smtp_Message_Interface $Message)
	{
		$this->Message = $Message;

		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Smtp_Message_Interface
	 */
	public function getMessage()
	{
		return $this->Message;
	}

}
