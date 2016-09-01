<?php

/**
 *
 *
 *
 */
abstract class Phools_Net_Smtp_Response_Abstract
implements Phools_Net_Smtp_Response_Interface
{

	/**
	 *
	 * @var string
	 */
	const CRLF = "\r\n";

	/**
	 *
	 * @param Phools_Stream_Input_Interface $Input
	 *
	 * @return Phools_Net_Smtp_Response_Abstract
	 */
	protected function skipWhitespace(Phools_Stream_Input_Interface &$Input)
	{
		while( ' ' == $Input->peek() )
		{
			$Input->forward();
		}

		return $this;
	}

	/**
	 *
	 * @param Phools_Stream_Input_Interface $Input
	 *
	 * @return Phools_Net_Smtp_Response_Abstract
	 */
	protected function readLine(Phools_Stream_Input_Interface &$Input)
	{
		$Line = '';
		while( self::CRLF != substr($Line, -2) )
		{
			$Line .= $Input->peek();
		}

		$Line = substr($Line, 0, -2);

		return $Line;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Response_Interface::receive()
	 */
	public function receive(Phools_Stream_Input_Interface &$Input)
	{
		$Code = $Input->peek(3);
		switch( $Code )
		{
			case '250':
				$Input->forward(3);
				break;

			/*
			 * @see RFC 5321, sec 4.3.2
			 *
			 * In addition to the codes listed below, any SMTP command can
			 * return any of the following codes if the corresponding unusual
			 * circumstances are encountered:
			 *
			 * 500  For the "command line too long" case or if the command name
			 * was not recognized. Note that producing a "command not
			 * recognized" error in response to the required subset of these
			 * commands is a violation of this specification. Similarly,
			 * producing a "command too long" message for a command line
			 * shorter than 512 characters would violate the provisions of
			 * Section 4.5.3.1.4.
			 */
			case '500':
				throw new Phools_Net_Smtp_Exception_CommandLineTooLong();
				break;

			/*
			 * @see RFC 5321, sec 4.3.2
			 *
			 * In addition to the codes listed below, any SMTP command can
			 * return any of the following codes if the corresponding unusual
			 * circumstances are encountered:
			 *
			 * 501  Syntax error in command or arguments.  In order to provide
			 * for future extensions, commands that are specified in this
			 * document as not accepting arguments (DATA, RSET, QUIT) SHOULD
			 * return a 501 message if arguments are supplied in the absence of
			 * EHLO- advertised extensions.
			 */
			case '501':
				throw new Phools_Net_Smtp_Exception_SyntaxError($Line);
				break;

			/*
			 * @see RFC 5321, sec 4.3.2
			 *
			 * In addition to the codes listed below, any SMTP command can
			 * return any of the following codes if the corresponding unusual
			 * circumstances are encountered:
			 *
			 *  421  Service shutting down and closing transmission channel
			 */
			case '421':
				throw new Phools_Net_Smtp_Exception_ClosingTransmissionChannel();
				break;

			default:
				throw new Phools_Net_Smtp_Exception_UnhandledResponseCode('');
		}

		$this->skipWhitespace($Input);
		$Line = $this->readLine($Input);

		return $this;
	}

}
