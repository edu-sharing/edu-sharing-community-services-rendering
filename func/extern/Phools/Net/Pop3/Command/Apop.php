<?php

/**
 * Implement POP3-command "APOP".
 * @see http://tools.ietf.org/html/rfc1939#page-15
 *
	APOP name digest

         Arguments:
             a string identifying a mailbox and a MD5 digest string
             (both required)

         Restrictions:
             may only be given in the AUTHORIZATION state after the POP3
             greeting or after an unsuccessful USER or PASS command

         Discussion:
             Normally, each POP3 session starts with a USER/PASS
             exchange.  This results in a server/user-id specific
             password being sent in the clear on the network.  For
             intermittent use of POP3, this may not introduce a sizable
             risk.  However, many POP3 client implementations connect to
             the POP3 server on a regular basis -- to check for new
             mail.  Further the interval of session initiation may be on
             the order of five minutes.  Hence, the risk of password
             capture is greatly enhanced.

             An alternate method of authentication is required which
             provides for both origin authentication and replay
             protection, but which does not involve sending a password
             in the clear over the network.  The APOP command provides
             this functionality.

             A POP3 server which implements the APOP command will
             include a timestamp in its banner greeting.  The syntax of
             the timestamp corresponds to the `msg-id' in [RFC822], and
             MUST be different each time the POP3 server issues a banner
             greeting.  For example, on a UNIX implementation in which a
             separate UNIX process is used for each instance of a POP3
             server, the syntax of the timestamp might be:

                <process-ID.clock@hostname>

             where `process-ID' is the decimal value of the process's
             PID, clock is the decimal value of the system clock, and
             hostname is the fully-qualified domain-name corresponding
             to the host where the POP3 server is running.

             The POP3 client makes note of this timestamp, and then
             issues the APOP command.  The `name' parameter has
             identical semantics to the `name' parameter of the USER
             command. The `digest' parameter is calculated by applying
             the MD5 algorithm [RFC1321] to a string consisting of the
             timestamp (including angle-brackets) followed by a shared
             secret.  This shared secret is a string known only to the
             POP3 client and server.  Great care should be taken to
             prevent unauthorized disclosure of the secret, as knowledge
             of the secret will allow any entity to successfully
             masquerade as the named user.  The `digest' parameter
             itself is a 16-octet value which is sent in hexadecimal
             format, using lower-case ASCII characters.

             When the POP3 server receives the APOP command, it verifies
             the digest provided.  If the digest is correct, the POP3
             server issues a positive response, and the POP3 session
             enters the TRANSACTION state.  Otherwise, a negative
             response is issued and the POP3 session remains in the
             AUTHORIZATION state.

             Note that as the length of the shared secret increases, so
             does the difficulty of deriving it.  As such, shared
             secrets should be long strings (considerably longer than
             the 8-character example shown below).

         Possible Responses:
             +OK maildrop locked and ready
             -ERR permission denied

         Examples:
             S: +OK POP3 server ready <1896.697170952@dbc.mtview.ca.us>
             C: APOP mrose c4c9334bac560ecc979e58001b3e22fb
             S: +OK maildrop has 1 message (369 octets)

             In this example, the shared  secret  is  the  string  `tan-
             staaf'.  Hence, the MD5 algorithm is applied to the string

                <1896.697170952@dbc.mtview.ca.us>tanstaaf

             which produces a digest value of

                c4c9334bac560ecc979e58001b3e22fb
 *
 */
class Phools_Net_Pop3_Command_Apop
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'APOP';
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

}
