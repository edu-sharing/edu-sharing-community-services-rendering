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
interface Phools_Net_Smtp_Extension_Interface
{

	/**
	 * Get the keyword identifying this extension.
	 *
	 * @return string
	 */
	public function getKeyword();

	/**
	 * Called before <link>Phools_Net_Smtp_Client_Interface</link> $Client
	 * send command <link>Phools_Net_Smtp_Command_Interface</link> $Command.
	 *
	 * @param Phools_Net_Smtp_Client_Interface $Client
	 * @param Phools_Net_Smtp_Command_Interface $Command
	 */
	public function preSendCommand(
		Phools_Net_Smtp_Client_Interface $Client,
		Phools_Net_Smtp_Command_Interface $Command);

	/**
	 * Called after <link>Phools_Net_Smtp_Client_Interface</link> $Client
	 * sent command <link>Phools_Net_Smtp_Command_Interface</link> $Command.
	 *
	 * @param Phools_Net_Smtp_Client_Interface $Client
	 * @param Phools_Net_Smtp_Command_Interface $Command
	 * @param Phools_Net_Smtp_Response_Interface $Response
	 */
	public function postSentCommand(
		Phools_Net_Smtp_Client_Interface $Client,
		Phools_Net_Smtp_Command_Interface $Command,
		Phools_Net_Smtp_Response_Interface $Response);

}
