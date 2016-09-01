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
interface Metacoon_Net_Smtp_Queue_Repository_Interface
{

	/**
	 * Returns generated message-id or false on failure.
	 *
	 * @param string $Message
	 * @param string $Sender
	 * @param array $Recipients
 	 * @param int $FailedDeliveryAttempts
	 * @param int $LastDeliveryAttempt
	 *
	 * @return string | false
	 */
	public function insert(
		$Message,
		$From,
		array $To,
		$FailedDeliveryAttempts,
		$LastDeliveryAttempt);

	/**
	 *
	 * @param array $Constraints
	 * @param string $Message
	 * @param string $Sender
	 * @param array $Recipients
	 * @param int $FailedDeliveryAttempts
	 * @param int $LastDeliveryAttempt
	 */
	public function update(
		array $Constraints,
		$Message,
		$Sender,
		array $Recipients,
		$FailedDeliveryAttempts,
		$LastDeliveryAttempt);

	/**
	 * Returns an array containing objects implementing
	 * Metacoon_Net_Smtp_Queue_Item_Interface.
	 *
	 * @param array $Constraints
	 * @param array $Order
	 * @param int $Limit
	 * @param int $Offset
	 *
	 * @return array
	 */
	public function findAll(
		array $Constraints = array(),
		array $Order = array(),
		$Limit = 1,
		$Offset = 0);

	/**
	 * Returns how many items have been deleted.
	 *
	 * @param array $Constraints
	 *
	 * @return int
	 */
	public function delete(array $Constraints);

}
