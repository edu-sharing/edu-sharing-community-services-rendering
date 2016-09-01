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
interface Phools_Net_Smtp_Response_Interface
{

	/**
	 *
	 * @param Phools_Stream_Input_Interface &$Input
	 *
	 * @throws Phools_Net_Smtp_Exception
	 * @throws Phools_Net_Smtp_Exception_AuthenticationRequired
	 * @throws Phools_Net_Smtp_Exception_AuthMechanismTooWeak
	 * @throws Phools_Net_Smtp_Exception_BadCommandSequence
	 * @throws Phools_Net_Smtp_Exception_ClosingTransmissionChannel
	 * @throws Phools_Net_Smtp_Exception_CommandNotImplemented
	 * @throws Phools_Net_Smtp_Exception_CommandParameterNotImplemented
	 * @throws Phools_Net_Smtp_Exception_EncryptionRequired
	 * @throws Phools_Net_Smtp_Exception_InsufficientSystemStorage
	 * @throws Phools_Net_Smtp_Exception_LocalErrorInProcessing
	 * @throws Phools_Net_Smtp_Exception_MailboxNameNotAllowed
	 * @throws Phools_Net_Smtp_Exception_MailboxNotAllowed
	 * @throws Phools_Net_Smtp_Exception_MailboxNotAvailable
	 * @throws Phools_Net_Smtp_Exception_MailboxTemporaryNotAvailable
	 * @throws Phools_Net_Smtp_Exception_MailFromOrRcptToParametersNotImplemented
	 * @throws Phools_Net_Smtp_Exception_PasswordTransitionRequired
	 * @throws Phools_Net_Smtp_Exception_StorageAllocationExceeded
	 * @throws Phools_Net_Smtp_Exception_SyntaxError
	 * @throws Phools_Net_Smtp_Exception_SyntaxErrorInParameters
	 * @throws Phools_Net_Smtp_Exception_TemporaryAuthenticationFailure
	 * @throws Phools_Net_Smtp_Exception_TransactionFailed
	 * @throws Phools_Net_Smtp_Exception_UnableToAccomodateParameters
	 * @throws Phools_Net_Smtp_Exception_UserNotLocal
	 *
	 *
	 * @return Phools_Net_Smtp_Response_Interface
	 *
	 */
	public function receive(Phools_Stream_Input_Interface &$Input);

}
