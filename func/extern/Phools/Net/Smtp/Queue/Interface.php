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
interface Metacoon_Net_Smtp_Message_Queue_Interface
{

	/**
	 *
	 * @param Metacoon_Net_Smtp_Message_Interface $Mail
	 * @param int $TimestampDelivery
	 */
	public function enqueue(Metacoon_Net_Smtp_Message_Interface $Mail, $TimestampDelivery);

	/**
	 *
	 * @param int $Limit
	 * @param int $Offset
	 *
	 * @return array
	 */
	public function fetchMails($Limit, $Offset = 0);

	/**
	 *
	 * @return Metacoon_Net_Smtp_Message_Queue_Interface
	 */
	public function process(Metacoon_Net_Smtp_Transport_Interface $Transport);

	/**
	 *
	 * @param int $MailPerHour
	 *
	 * @return Metacoon_Net_Smtp_Adapter_Interface
	 */
	public function setMailsPerHour($MailPerHour);

	/**
	 * Set this to true if mails with couldn't be sent sucessfully are to be
	 *
	 * @param bool $requeueOnError
	 *
	 * @return Metacoon_Net_Smtp_Adapter_Interface
	 */
	public function requeueOnError($RequeueOnError);

}
