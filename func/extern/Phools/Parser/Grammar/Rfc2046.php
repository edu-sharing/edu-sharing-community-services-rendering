<?php

class Phools_Parser_Grammar_Rfc2046
extends Phools_Parser_Grammar_Abstract
{

	/**
	 *
	 */
	public function __construct()
	{
//     boundary := 0*69<bchars> bcharsnospace
		$this->define('boundary', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Repetition(0, 69,
				new Phools_Parser_Rule_Definition('bchars')),
			new Phools_Parser_Rule_Definition('bcharsnospace'),
		)));

//     bchars := bcharsnospace / " "
		$this->define('bchars', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('bcharsnospace'),
			new Phools_Parser_Rule_Char(' '),
		)));

//     bcharsnospace := DIGIT / ALPHA / "'" / "(" / ")" /
//                      "+" / "_" / "," / "-" / "." /
//                      "/" / ":" / "=" / "?"
		$this->define('bcharsnospace', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('DIGIT'),
			new Phools_Parser_Rule_Definition('ALPHA'),
			new Phools_Parser_Rule_Char("'"),
			new Phools_Parser_Rule_Char('('),
			new Phools_Parser_Rule_Char(')'),
			new Phools_Parser_Rule_Char('+'),
			new Phools_Parser_Rule_Char('_'),
			new Phools_Parser_Rule_Char(','),
			new Phools_Parser_Rule_Char('-'),
			new Phools_Parser_Rule_Char('.'),
			new Phools_Parser_Rule_Char('/'),
			new Phools_Parser_Rule_Char(':'),
			new Phools_Parser_Rule_Char('='),
			new Phools_Parser_Rule_Char('?'),
			)));

//     body-part := <"message" as defined in RFC 822, with all
//                   header fields optional, not starting with the
//                   specified dash-boundary, and with the
//                   delimiter not occurring anywhere in the
//                   body part.  Note that the semantics of a
//                   part differ from the semantics of a message,
//                   as described in the text.>

//     close-delimiter := delimiter "--"
		$this->define('close-delimiter', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Definition('delimiter'),
			new Phools_Parser_Rule_Keyword('--'),
		)));

//     dash-boundary := "--" boundary
//                      ; boundary taken from the value of
//                      ; boundary parameter of the
//                      ; Content-Type field.
		$this->define('dash-boundary', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Keyword('--'),
			new Phools_Parser_Rule_Definition('boundary'),
			)));

//     delimiter := CRLF dash-boundary
		$this->define('delimiter', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Definition('CRLF'),
			new Phools_Parser_Rule_Keyword('dash-boundary'),
			)));

//     discard-text := *(*text CRLF)
//                     ; May be ignored or discarded.

//     encapsulation := delimiter transport-padding
//                      CRLF body-part

//     epilogue := discard-text

//     multipart-body := [preamble CRLF]
//                       dash-boundary transport-padding CRLF
//                       body-part *encapsulation
//                       close-delimiter transport-padding
//                       [CRLF epilogue]

//     preamble := discard-text

//     transport-padding := *LWSP-char
//                          ; Composers MUST NOT generate
//                          ; non-zero length transport
//                          ; padding, but receivers MUST
//                          ; be able to handle padding
//                          ; added by message transports.
	}

}
