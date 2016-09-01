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
class Metacoon_Net_Smtp_Queue_Item_Repository
extends Metacoon_Net_Smtp_Queue_Item_Abstract
{

	/**
	 *
	 * @param Metacoon_Net_Smtp_Message_Interface $Message
	 * @param int $Priority
	 */
	protected function __construct(
		Metacoon_Net_Smtp_Queue_Repository_Interface $Repository,
		$Id,
		$Message,
		$Sender,
		array $Recipients,
		$FailedDeliveryAttempts = 0,
		$LastDeliveryAttempt = 0)
	{
		parent::__construct(
			$Id,
			$Message,
			$Sender,
			$Recipients,
			$FailedDeliveryAttempts,
			$LastDeliveryAttempt);

		$this->setRepository($Repository);
	}

	/**
	 * Garbage collect.
	 *
	 */
	public function __destruct()
	{
		$this->Repository = null;

		parent::__destruct();
	}

	/**
	 * Send this item using given $Transport.
	 *
	 * @param Metacoon_Net_Smtp_Transport_Interface $Transport
	 * @throws Metacoon_Net_Smtp_Queue_Exception
	 */
	public function send(Metacoon_Net_Smtp_Transport_Interface $Transport)
	{
		if ( ! $Transport->send($this->getMessage(), $this->getSender(), $this->getRecipients()) )
		{
			$this
				->increaseFailedDeliveryAttempts()
				->setLastDeliveryAttempt(time());

			if ( ! $this->update() )
			{
				throw new Metacoon_Net_Smtp_Queue_Exception('Error updating queue-item.');
			}
		}

		if ( ! $this->delete() )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('Error deleting sent queue-item.');
		}

		return true;
	}

	/**
	 * Create a new queue-item by storing it in the given repository.
	 *
	 * @param Metacoon_Net_Smtp_Queue_Repository_Interface $Repository
	 * @param string
	 * @param string
	 * @param array()
	 */
	public static function create(
		Metacoon_Net_Smtp_Queue_Repository_Interface $Repository,
		$Message,
		$Sender,
		array $Recipients)
	{
		$FailedDeliveryAttempts = 0;
		$LastDeliveryAttempt = 0;

		$Id = $Repository->insert(
			$Message,
			$Sender,
			$Recipients,
			$FailedDeliveryAttempts,
			$LastDeliveryAttempt);

		$Item = new Metacoon_Net_Smtp_Queue_Item_Repository(
			$Repository,
			$Id,
			$Message,
			$Sender,
			$Recipients,
			$FailedDeliveryAttempts,
			$LastDeliveryAttempt);

		return $Item;
	}

	/**
	 * Find all queue-items which match given criteria.
	 *
	 * @param Metacoon_Net_Smtp_Queue_Repository_Interface $Repository
	 * @param array $Constraints
	 */
	public static function findAll(
		Metacoon_Net_Smtp_Queue_Repository_Interface $Repository,
		array $Constraints)
	{
		$Rowset = $Repository->findAll($Constraints);

		$Items = array();
		foreach( $Rowset as $Row )
		{
			$Items[] = new Metacoon_Net_Smtp_Queue_Item_Repository(
				$Repository,
				$Row[Metacoon_Net_Smtp_Queue_Item_Interface::ITEM_ID],
				$Row[Metacoon_Net_Smtp_Queue_Item_Interface::MESSAGE],
				$Row[Metacoon_Net_Smtp_Queue_Item_Interface::SENDER],
				$Row[Metacoon_Net_Smtp_Queue_Item_Interface::RECIPIENTS],
				$Row[Metacoon_Net_Smtp_Queue_Item_Interface::FAILED_DELIVERY_ATTEMPTS],
				$Row[Metacoon_Net_Smtp_Queue_Item_Interface::LAST_DELIVERY_ATTEMPT]);
		}

		return $Items;
	}

	/**
	 * Update this item's data in repository.
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	protected function update()
	{
		if ( ! $this->getId() )
		{
			throw new Exception('No message-id set.');
		}

		$Repository = $this->getRepository();
		$Result = $Repository->update(
			array(
				Metacoon_Net_Smtp_Queue_Constraint::ITEM_ID_EQUALS => $this->getId(),
			),
			$this->getMessage(),
			$this->getSender(),
			$this->getRecipients(),
			$this->getFailedDeliveryAttempts(),
			$this->getLastDeliveryAttempt()
		);

		if ( ! $Result )
		{
			throw new Exception('Error updating smtp-queue-item.');
		}

		return $this;
	}

	/**
	 * Delete this queue-item. Return true on success, false on error.
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function delete()
	{
		if ( ! $this->getId() )
		{
			throw new Exception('No message-id set.');
		}

		$Repository = $this->getRepository();
		if ( ! $Repository->delete(array(
				Metacoon_Net_Smtp_Queue_Constraint::ITEM_ID_EQUALS => $this->getId(),))
			)
		{
			return false;
		}

		$this->setId(0);

		return true;
	}

	/**
	 *
	 *
	 * @var Metacoon_Net_Smtp_Queue_Repository_Interface
	 */
	protected $Repository = null;

	/**
	 *
	 *
	 * @param Metacoon_Net_Smtp_Queue_Repository_Interface $Repository
	 * @return Metacoon_Net_Smtp_Queue_Item_Repository
	 */
	public function setRepository(
		Metacoon_Net_Smtp_Queue_Repository_Interface $Repository)
	{
		$this->Repository = $Repository;
		return $this;
	}

	/**
	 *
	 * @return Metacoon_Net_Smtp_Queue_Repository_Interface
	 */
	protected function getRepository()
	{
		return $this->Repository;
	}

}
