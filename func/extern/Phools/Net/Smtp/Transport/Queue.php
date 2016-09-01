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
 * Implements mail transportation using an queue to send messages sequentially.
 *
 *
 */
class Metacoon_Net_Smtp_Transport_Queue
implements
	Phools_Net_Smtp_Transport_Interface
{

	/**
	 *
	 * @param Metacoon_Net_Smtp_Queue_Repository_Interface $Repository
	 */
	public function __construct(
		Metacoon_Net_Smtp_Queue_Repository_Interface $Repository)
	{
		$this->setRepository($Repository);
	}

	/**
	 * Free repository.
	 *
	 */
	public function __destruct()
	{
		$this->Repository = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Transport_Interface::send()
	 */
	public function send(Phools_Net_Smtp_Message_Interface $Message, $From, array $To)
	{
		$Repository = $this->getRepository();

		$QueueItem = $Repository->insert($Message, $From, $To, 0, 0);
		if ( ! $QueueItem )
		{
			return false;
		}

		return $this;
	}

	/**
	 *
	 *
	 * @var Metacoon_Net_Smtp_Queue_Respository
	 */
	private $Repository = null;

	/**
	 *
	 *
	 * @param Metacoon_Net_Smtp_Queue_Respository $Repository
	 * @return Metacoon_Net_Smtp_Transport_Queue
	 */
	public function setRepository(
		Metacoon_Net_Smtp_Queue_Respository $Repository)
	{
		$this->Repository = $Repository;
		return $this;
	}

	/**
	 *
	 * @return Metacoon_Net_Smtp_Queue_Respository
	 */
	protected function getRepository()
	{
		return $this->Repository;
	}

}
