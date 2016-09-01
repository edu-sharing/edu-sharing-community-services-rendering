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
 * Implementing a trace-block inside a message's header-section.
 *
 *
 */
interface Phools_Net_Smtp_Trace_Interface
{

	/**
	 *
	 * @param DateTime $Date;
	 *
	 * @return Phools_Net_Smtp_Trace_Interface
	 */
	public function setResentDate(DateTime $Date);

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Mailbox $Mailbox
	 *
	 * @return Phools_Net_Smtp_Trace_Interface
	 */
	public function addResentFrom(Phools_Net_Smtp_Address_Mailbox $Mailbox);

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Mailbox $Mailbox
	 *
	 * @return Phools_Net_Smtp_Trace_Interface
	 */
	public function setResentSender(Phools_Net_Smtp_Address_Mailbox $Mailbox);

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Interface $Address
	 *
	 * @return Phools_Net_Smtp_Trace_Interface
	 */
	public function addResentTo(Phools_Net_Smtp_Address_Interface $Address);

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Interface $Address
	 *
	 * @return Phools_Net_Smtp_Trace_Interface
	 */
	public function addResentCc(Phools_Net_Smtp_Address_Interface $Address);

	/**
	 *
	 * @param Phools_Net_Smtp_Address_Interface $Address
	 *
	 * @return Phools_Net_Smtp_Trace_Interface
	 */
	public function addResentBcc(Phools_Net_Smtp_Address_Interface $Address);

	/**
	 *
	 * @param string $MessageId
	 *
	 * @return Phools_Net_Smtp_Trace_Interface
	 */
	public function setResentMessageId($MessageId);

}
