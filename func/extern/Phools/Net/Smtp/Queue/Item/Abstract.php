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
abstract class Metacoon_Net_Smtp_Queue_Item_Abstract
implements Metacoon_Net_Smtp_Queue_Item_Interface
{

	/**
	 *
	 * @param Metacoon_Net_Smtp_Message_Interface $Message
	 * @param int $LastDeliveryAttempt
	 */
	protected function __construct(
		$Id,
		$Message,
		$Sender,
		array $Recipients,
		$FailedDeliveryAttempts,
		$LastDeliveryAttempt)
	{
		$this
			->setId($Id)
			->setMessage($Message)
			->setSender($Sender)
			->setFailedDeliveryAttempts($FailedDeliveryAttempts)
			->setLastDeliveryAttempt($LastDeliveryAttempt);

		foreach( $Recipients as $Recipient )
		{
			$this->addRecipient($Recipient);
		}
	}

	/**
	 * Garbage collect.
	 *
	 */
	public function __destruct()
	{
		$this->LastDeliveryAttempt = null;
		$this->FailedDeliveryAttempts = null;
		$this->Recipients = null;
		$this->Sender = null;
		$this->Message = null;
		$this->Id = null;
	}

	/**
	 *
	 * @var string
	 */
	private $Id = '';

	/**
	 *
	 * @param string $Id
	 *
	 * @return Metacoon_Net_Smtp_Queue_Item
	 */
	protected function setId($Id)
	{
		$this->Id = (string) $Id;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->Id;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Message = '';

	/**
	 *
	 *
	 * @param string $Message
	 *
	 * @return Metacoon_Net_Smtp_Queue_Item
	 */
	protected function setMessage($Message)
	{
		$this->Message = (string) $Message;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->Message;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Sender = '';

	/**
	 *
	 *
	 * @param string $Sender
	 *
	 * @return Metacoon_Net_Smtp_Queue_Item
	 */
	protected function setSender($Sender)
	{
		$this->Sender = (string) $Sender;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getSender()
	{
		return $this->Sender;
	}

	/**
	 *
	 *
	 * @var array
	 */
	private $Recipients = array();

	/**
	 *
	 *
	 * @param string $Recipient
	 *
	 * @return Metacoon_Net_Smtp_Queue_Item
	 */
	public function addRecipient($Recipient)
	{
		if ( ! in_array($Recipient, $this->Recipients) )
		{
			$this->Recipients[] = (string) $Recipient;
		}

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getRecipients()
	{
		return $this->Recipients;
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $LastDeliveryAttempt = 0;

	/**
	 *
	 *
	 * @param int $LastDeliveryAttempt
	 *
	 * @return Metacoon_Net_Smtp_Queue_Item
	 */
	public function setLastDeliveryAttempt($LastDeliveryAttempt)
	{
		$this->LastDeliveryAttempt = (int) $LastDeliveryAttempt;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function getLastDeliveryAttempt()
	{
		return $this->LastDeliveryAttempt;
	}

	/**
	 * Hold the timestamp this queue-item was created.
	 *
	 * @var int
	 */
	private $FailedDeliveryAttempts = 0;

	/**
	 * Set counter of failed delivery-attempts.
	 *
	 * @param int $FailedDeliveryAttempts
	 *
	 * @return Metacoon_Net_Smtp_Queue_Item_Abstract
	 */
	protected function setFailedDeliveryAttempts($FailedDeliveryAttempts)
	{
		$this->FailedDeliveryAttempts = (int) $FailedDeliveryAttempts;
		return $this;
	}

	/**
	 * Increase counter for failed delivery-attempts.
	 *
	 * @return Metacoon_Net_Smtp_Queue_Item_Abstract
	 */
	protected function increaseFailedDeliveryAttempts()
	{
		$this->FailedDeliveryAttempts++;
		return $this;
	}

	/**
	 * Get the UNIX-timestamp this queue-item was created.
	 *
	 * @return int
	 */
	public function getFailedDeliveryAttempts()
	{
		return $this->FailedDeliveryAttempts;
	}

}
