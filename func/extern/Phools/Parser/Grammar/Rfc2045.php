<?php

class Phools_Parser_Grammar_Rfc2045
extends Phools_Parser_Grammar_Abstract
{

	/**
	 *
	 */
	public function __construct()
	{
		//	version := "MIME-Version" ":" 1*DIGIT "." 1*DIGIT
		$this->define('version', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Keyword('MIME-Version:'),
			new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
				new Phools_Parser_Rule_Definition('CFWS')),
			new Phools_Parser_Rule_Repetition(1, PHP_INT_SIZE,
				new Phools_Parser_Rule_Definition('DIGIT')),
			new Phools_Parser_Rule_Char('.'),
			new Phools_Parser_Rule_Repetition(1, PHP_INT_SIZE,
				new Phools_Parser_Rule_Definition('DIGIT')),
			new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
				new Phools_Parser_Rule_Definition('CFWS')),
			)));

		// content := "Content-Type" ":" type "/" subtype
		//                *(";" parameter)
		//                ; Matching of media type and subtype
		//                ; is ALWAYS case-insensitive.
		$this->define('content', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Keyword('Content-Type:'),
			new Phools_Parser_Rule_Definition('type'),
			new Phools_Parser_Rule_Char('.'),
			new Phools_Parser_Rule_Definition('subtype'),
			new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
				new Phools_Parser_Rule_Definition('parameter')),
		)));

		//     type := discrete-type / composite-type
		$this->define('type', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('discrete-type'),
			new Phools_Parser_Rule_Definition('composite-type'),
		)));

		//     discrete-type := "text" / "image" / "audio" / "video" /
		//                      "application" / extension-token
		$this->define('discrete-type', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('text'),
			new Phools_Parser_Rule_Definition('image'),
			new Phools_Parser_Rule_Definition('audio'),
			new Phools_Parser_Rule_Definition('video'),
			new Phools_Parser_Rule_Definition('application'),
			new Phools_Parser_Rule_Definition('extension-token'),
		)));

		//     composite-type := "message" / "multipart" / extension-token
		$this->define('composite-type', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('message'),
			new Phools_Parser_Rule_Definition('multipart'),
			new Phools_Parser_Rule_Definition('extension-token'),
		)));

		//     extension-token := ietf-token / x-token
		$this->define('extension-token', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('ietf-token'),
			new Phools_Parser_Rule_Definition('x-token'),
		)));

		//     ietf-token := <An extension token defined by a
		//                    standards-track RFC and registered
		//                    with IANA.>
		$this->define('ietf-token', new Phools_Parser_Rule_Definition('token'));

		//     x-token := <The two characters "X-" or "x-" followed, with
		//                 no intervening white space, by any token>
		$this->define('x-token', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Keyword('x-'),
				new Phools_Parser_Rule_Keyword('X-'),
			)),
			new Phools_Parser_Rule_Definition('token'),
		)));

		//     subtype := extension-token / iana-token
		$this->define('subtype', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('extension-token'),
			new Phools_Parser_Rule_Definition('iana-token'),
		)));

		//     iana-token := <A publicly-defined extension token. Tokens
		//                    of this form must be registered with IANA
		//                    as specified in RFC 2048.>
		$this->define('iana-token', new Phools_Parser_Rule_Definition('token'));

		//     parameter := attribute "=" value
		$this->define('parameter', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Definition('attribute'),
			new Phools_Parser_Rule_Char('='),
			new Phools_Parser_Rule_Definition('value'),
		)));

		//     attribute := token
		//                  ; Matching of attributes
		//                  ; is ALWAYS case-insensitive.
		$this->define('attribute', new Phools_Parser_Rule_Definition('attribute'));

		//     value := token / quoted-string
		$this->define('value', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('token'),
			new Phools_Parser_Rule_Definition('quoted-string'),
		)));

		//     token := 1*<any (US-ASCII) CHAR except SPACE, CTLs,
		//                 or tspecials>
		$this->define('token', new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
			new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('ALPHA'),
			new Phools_Parser_Rule_Definition('DIGIT'),
		))));

		//     tspecials :=  "(" / ")" / "<" / ">" / "@" /
		//                   "," / ";" / ":" / "\" / <">
		//                   "/" / "[" / "]" / "?" / "="
		//                   ; Must be in quoted-string,
		//                   ; to use within parameter values
		$this->define('tspecials', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Char('('),
			new Phools_Parser_Rule_Char(')'),
			new Phools_Parser_Rule_Char('<'),
			new Phools_Parser_Rule_Char('>'),
			new Phools_Parser_Rule_Char('@'),
			new Phools_Parser_Rule_Char(','),
			new Phools_Parser_Rule_Char(';'),
			new Phools_Parser_Rule_Char(':'),
			new Phools_Parser_Rule_Char('\\'),
			new Phools_Parser_Rule_Char('"'),
			new Phools_Parser_Rule_Char('/'),
			new Phools_Parser_Rule_Char('['),
			new Phools_Parser_Rule_Char(']'),
			new Phools_Parser_Rule_Char('?'),
			new Phools_Parser_Rule_Char('='),
		)));

		//     encoding := "Content-Transfer-Encoding" ":" mechanism
		$this->define('encoding', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Keyword('MIME-Version:'),
			new Phools_Parser_Rule_Definition('mechanism'),
		)));

		//     mechanism := "7bit" / "8bit" / "binary" /
		//                  "quoted-printable" / "base64" /
		//                  ietf-token / x-token
		$this->define('mechanism', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Keyword('7bit'),
			new Phools_Parser_Rule_Keyword('8bit'),
			new Phools_Parser_Rule_Keyword('binary'),
			new Phools_Parser_Rule_Keyword('quoted-printable'),
			new Phools_Parser_Rule_Keyword('base64'),
			new Phools_Parser_Rule_Keyword('ietf-token'),
			new Phools_Parser_Rule_Keyword('x-token'),
		)));

		//     quoted-printable := qp-line *(CRLF qp-line)
		$this->define('quoted-printable', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Definition('qp-line'),
			new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
				new Phools_Parser_Rule_Sequence(array(
					new Phools_Parser_Rule_Definition('CRLF'),
					new Phools_Parser_Rule_Definition('qp-line'),
			))),
		)));

		//     qp-line := *(qp-segment transport-padding CRLF)
		//                qp-part transport-padding
		$this->define('qp-line', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
				new Phools_Parser_Rule_Sequence(array(
					new Phools_Parser_Rule_Definition('qp-segment'),
					new Phools_Parser_Rule_Definition('transport-padding'),
					new Phools_Parser_Rule_Definition('CRLF'),
			))),
		)));

		//     qp-part := qp-section
		//                ; Maximum length of 76 characters
		$this->define('qp-part', new Phools_Parser_Rule_Definition('qp-section'));

		//     qp-segment := qp-section *(SPACE / TAB) "="
		//                   ; Maximum length of 76 characters
		$this->define('qp-segment', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Definition('qp-section'),
			new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
			new Phools_Parser_Rule_Definition('WSP')),
		)));

		//     qp-section := [*(ptext / SPACE / TAB) ptext]

		//     ptext := hex-octet / safe-char
		$this->define('ptext', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('hex-octet'),
			new Phools_Parser_Rule_Definition('safe-char'),
		)));

		//     safe-char := <any octet with decimal value of 33 through
		//                  60 inclusive, and 62 through 126>
		//                  ; Characters not listed as "mail-safe" in
		//                  ; RFC 2049 are also not recommended.
		$this->define('safe-char', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_AsciiRange(33, 60),
			new Phools_Parser_Rule_AsciiRange(62, 126),
		)));

		//     hex-octet := "=" 2(DIGIT / "A" / "B" / "C" / "D" / "E" / "F")
		//                  ; Octet must be used for characters > 127, =,
		//                  ; SPACEs or TABs at the ends of lines, and is
		//                  ; recommended for any character not listed in
		//                  ; RFC 2049 as "mail-safe".
		$this->define('hex-octet', new Phools_Parser_Rule_Repetition(2, 2,
			new Phools_Parser_Rule_Definition('HEXDIG')));

		//     transport-padding := *LWSP-char
		//                          ; Composers MUST NOT generate
		//                          ; non-zero length transport
		//                          ; padding, but receivers MUST
		//                          ; be able to handle padding
		//                          ; added by message transports.
		$this->define('transport-padding', new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
			new Phools_Parser_Rule_Definition('LWSP')));

		//     id := "Content-ID" ":" msg-id
		$this->define('id', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Keyword('Content-ID:'),
			new Phools_Parser_Rule_Definition('msg-id'),
		)));

		//     description := "Content-Description" ":" *text
		$this->define('description', new Phools_Parser_Rule_Sequence(array(
			new Phools_Parser_Rule_Keyword('Content-Description:'),
			new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
				new Phools_Parser_Rule_Definition('text')),
		)));

		//     MIME-extension-field := <Any RFC 822 header field which
		//                              begins with the string
		//                              "Content-">
		$this->define('MIME-extension-field', new Phools_Parser_Rule_Sequence(array(
		)));

		$this->define('fields', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_Definition('version'),
		)));

	}

}
