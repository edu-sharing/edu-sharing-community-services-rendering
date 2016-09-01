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
abstract class Metacoon_Net_Smtp_Queue_Order
{

	/**
	 * Used to sort ascending by timestamp of last delivery-attempt.
	 *
	 * @var string
	 */
	const LAST_DELIVERY_ATTEMPT_ASC = 'LAST_DELIVERY_ATTEMPT_ASC';

	/**
	 * Used to sort descending by timestamp of last delivery-attempt.
	 *
	 * @var string
	 */
	const LAST_DELIVERY_ATTEMPT_DESC = 'LAST_DELIVERY_ATTEMPT_DESC';

	/**
	 * Used to sort ascending by failed delivery-attempts.
	 *
	 * @var string
	 */
	const FAILED_DELIVERY_ATTEMPTS_ASC = 'FAILED_DELIVERY_ATTEMPTS_ASC';

	/**
	 * Used to sort descending by failed delivery-attempts.
	 *
	 * @var string
	 */
	const FAILED_DELIVERY_ATTEMPTS_DESC = 'FAILED_DELIVERY_ATTEMPTS_DESC';

}
