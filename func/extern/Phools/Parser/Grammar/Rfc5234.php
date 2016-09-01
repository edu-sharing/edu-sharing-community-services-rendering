<?php

/**
 * Intended to serve as a base-class for defining other parsers implementing
 * a certain, possibly RFC-defined, grammar.
 *
 *
 */
class Phools_Parser_Grammar_Rfc5234
extends Phools_Parser_Grammar_Abstract
{

	/**
	 *
	 *
	 */
	public function __construct()
	{
//         ALPHA          =  %x41-5A / %x61-7A	; A-Z / a-z
		$this->define('ALPHA', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_AsciiRange(0x41, 0x5a),
			new Phools_Parser_Rule_AsciiRange(0x61, 0x7a),
			)));

//         BIT            =  "0" / "1"
		$this->define('BIT', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Char('0'),
			new Phools_Parser_Rule_Char('1'),
			)));

//         CR             =  %x0D				; carriage return
		$this->define('CR', new Phools_Parser_Rule_AsciiCode(0x0d));

//         CRLF           =  CR LF				; Internet standard newline
		$this->define('CRLF', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Definition('CR'),
			new Phools_Parser_Rule_Definition('LF'),
		)));

//         CHAR           =  %x01-7F
		$this->define('CHAR', new Phools_Parser_Rule_AsciiRange(0x01, 0x7f));

//         CTL            =  %x00-1F / %x7F		; controls
		$this->define('CTL', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_AsciiRange(0x00, 0x1f),
			new Phools_Parser_Rule_AsciiCode(0x7f),
		)));

//         DIGIT          =  %x30-39			; 0-9
		$this->define('DIGIT', new Phools_Parser_Rule_AsciiRange(0x30, 0x39));

//         DQUOTE         =  %x22				; " (Double Quote)
		$this->define('DQUOTE', new Phools_Parser_Rule_AsciiCode(0x22));

//         HEXDIG         =  DIGIT / "A" / "B" / "C" / "D" / "E" / "F"
		$this->define('HEXDIG', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('DIGIT'),
			new Phools_Parser_Rule_AsciiRange(0x41, 0x46),
		)));

//         HTAB           =  %x09				; horizontal tab
		$this->define('HTAB', new Phools_Parser_Rule_AsciiCode(0x09));

//         LF             =  %x0A				; linefeed
		$this->define('LF', new Phools_Parser_Rule_AsciiCode(0x0a));

//         LWSP           =  *(WSP / CRLF WSP)
//                                ; Use of this linear-white-space rule
//                                ;  permits lines containing only white
//                                ;  space that are no longer legal in
//                                ;  mail headers and have caused
//                                ;  interoperability problems in other
//                                ;  contexts.
//                                ; Do not use when defining mail
//                                ;  headers and use with caution in
//                                ;  other contexts.
		$this->define('LWSP', new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
			new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('WSP'),
				new Phools_Parser_Rule_Sequence(array(
					new Phools_Parser_Rule_Definition('CRLF'),
					new Phools_Parser_Rule_Definition('WSP'),
				))
			))));

//         OCTET          =  %x00-FF			; 8 bits of data
		$this->define('OCTET', new Phools_Parser_Rule_AsciiRange(0x00, 0xff));

//         SP             =  %x20
		$this->define('SP', new Phools_Parser_Rule_AsciiCode(0x20));

//         VCHAR          =  %x21-7E			; visible (printing) characters
		$this->define('VCHAR', new Phools_Parser_Rule_AsciiRange(0x21, 0x7e));

//         WSP            =  SP / HTAB			; white space
		$this->define('WSP', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('SP'),
			new Phools_Parser_Rule_Definition('HTAB'),
			)));
	}

}
