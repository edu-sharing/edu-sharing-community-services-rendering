<?php

/**
 *
 *
 *
 */
class Phools_Parser_Grammar_Rfc5322
extends Phools_Parser_Grammar_Abstract
{

	/**
	 *
	 */
	public function __construct()
	{
		//   quoted-pair     =   ("\" (VCHAR / WSP)) / obs-qp
		$this->define('quoted-pair', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Char('\\'),
				new Phools_Parser_Rule_Alternative(array(
						new Phools_Parser_Rule_Definition('VCHAR'),
						new Phools_Parser_Rule_Definition('WSP'),
				))
		)));

		//   FWS             =   ([*WSP CRLF] 1*WSP) /  obs-FWS		; Folding white space
		$this->define('FWS', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
										new Phools_Parser_Rule_Definition('WSP')),
								new Phools_Parser_Rule_Definition('CRLF')))),
				new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('WSP')),
		)));

		//   ctext           =   %d33-39 /          ; Printable US-ASCII
		//                       %d42-91 /          ;  characters not including
		//                       %d93-126 /         ;  "(", ")", or "\"
		//                       obs-ctext
		$this->define('ctext', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_AsciiRange(33, 39),
				new Phools_Parser_Rule_AsciiRange(42, 91),
				new Phools_Parser_Rule_AsciiRange(93, 126),
		)));

		//   ccontent        =   ctext / quoted-pair / comment
		$this->define('ccontent', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('ctext'),
				new Phools_Parser_Rule_Definition('quoted-pair'),
				new Phools_Parser_Rule_Definition('comment'),
		)));

		//   comment         =   "(" *([FWS] ccontent) [FWS] ")"
		$this->define('comment', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Char('('),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Repetition(0, 1,
										new Phools_Parser_Rule_Definition('FWS')),
								new Phools_Parser_Rule_Definition('ccontent')))),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('FWS')),
				new Phools_Parser_Rule_Char(')'),
		)));

		//   CFWS            =   (1*([FWS] comment) [FWS]) / FWS
		$this->define('CFWS', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Sequence(array(
						new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
								new Phools_Parser_Rule_Sequence(array(
										new Phools_Parser_Rule_Repetition(0, 1,
												new Phools_Parser_Rule_Definition('FWS')),
										new Phools_Parser_Rule_Definition('comment'),
								))),
						new Phools_Parser_Rule_Repetition(0, 1,
								new Phools_Parser_Rule_Definition('FWS')),
				)),
				new Phools_Parser_Rule_Definition('FWS'),
		)));

		//   atext           =   ALPHA / DIGIT /    ; Printable US-ASCII
		//                       "!" / "#" /        ;  characters not including
		//                       "$" / "%" /        ;  specials.  Used for atoms.
		//                       "&" / "'" /
		//                       "*" / "+" /
		//                       "-" / "/" /
		//                       "=" / "?" /
		//                       "^" / "_" /
		//                       "`" / "{" /
		//                       "|" / "}" /
		//                       "~"
		$this->define('atext', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('ALPHA'),
				new Phools_Parser_Rule_Definition('DIGIT'),
				new Phools_Parser_Rule_Char('!'),
				new Phools_Parser_Rule_Char('#'),
				new Phools_Parser_Rule_Char('$'),
				new Phools_Parser_Rule_Char('%'),
				new Phools_Parser_Rule_Char('&'),
				new Phools_Parser_Rule_Char("'"),
				new Phools_Parser_Rule_Char('*'),
				new Phools_Parser_Rule_Char('+'),
				new Phools_Parser_Rule_Char('-'),
				new Phools_Parser_Rule_Char('/'),
				new Phools_Parser_Rule_Char('='),
				new Phools_Parser_Rule_Char('?'),
				new Phools_Parser_Rule_Char('^'),
				new Phools_Parser_Rule_Char('_'),
				new Phools_Parser_Rule_Char('`'),
				new Phools_Parser_Rule_Char('{'),
				new Phools_Parser_Rule_Char('|'),
				new Phools_Parser_Rule_Char('}'),
				new Phools_Parser_Rule_Char('~'),
		)));

		//   atom            =   [CFWS] 1*atext [CFWS]
		$this->define('atom', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
				new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('atext')),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
		)));

		//   dot-atom-text   =   1*atext *("." 1*atext)
		$this->define('dot-atom-text', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('atext')),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Char('.'),
								new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
										new Phools_Parser_Rule_Definition('atext')),
						))),
				)));

		//   dot-atom        =   [CFWS] dot-atom-text [CFWS]
		$this->define('dot-atom', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
				new Phools_Parser_Rule_Definition('dot-atom-text'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
		)));

		//   specials        =   "(" / ")" /        ; Special characters that do
		//                       "<" / ">" /        ;  not appear in atext
		//                       "[" / "]" /
		//                       ":" / ";" /
		//                       "@" / "\" /
		//                       "," / "." /
		//                       DQUOTE
		$this->define('specials', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Char('('),
				new Phools_Parser_Rule_Char(')'),
				new Phools_Parser_Rule_Char('<'),
				new Phools_Parser_Rule_Char('>'),
				new Phools_Parser_Rule_Char('['),
				new Phools_Parser_Rule_Char(']'),
				new Phools_Parser_Rule_Char(':'),
				new Phools_Parser_Rule_Char(';'),
				new Phools_Parser_Rule_Char('@'),
				new Phools_Parser_Rule_Char('\\'),
				new Phools_Parser_Rule_Char(','),
				new Phools_Parser_Rule_Char('.'),
				new Phools_Parser_Rule_Definition('DQUOTE'),
		)));

		//   qtext           =   %d33 /             ; Printable US-ASCII
		//                       %d35-91 /          ;  characters not including
		//                       %d93-126 /         ;  "\" or the quote character
		//                       obs-qtext
		$this->define('qtext', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_AsciiCode(33),
				new Phools_Parser_Rule_AsciiRange(35, 91),
				new Phools_Parser_Rule_AsciiRange(93, 126),
		)));

		//   qcontent        =   qtext / quoted-pair
		$this->define('qcontent', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('qtext'),
				new Phools_Parser_Rule_Definition('quoted-pair'),
		)));

		//   quoted-string   =   [CFWS]
		//                       DQUOTE *([FWS] qcontent) [FWS] DQUOTE
		//                       [CFWS]
		$this->define('quoted-string', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
				new Phools_Parser_Rule_Definition('DQUOTE'),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Repetition(0, 1,
										new Phools_Parser_Rule_Definition('FWS')),
								new Phools_Parser_Rule_Definition('qcontent'),
						))),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('FWS')),
				new Phools_Parser_Rule_Definition('DQUOTE'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
		)));

		//   word            =   atom / quoted-string
		$this->define('word', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('atom'),
				new Phools_Parser_Rule_Definition('quoted-string'),
		)));

		//   phrase          =   1*word / obs-phrase
		$this->define('phrase', new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
				new Phools_Parser_Rule_Definition('word')));

		//   unstructured    =   (*([FWS] VCHAR) *WSP) / obs-unstruct
		$this->define('unstructured', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Repetition(0, 1,
										new Phools_Parser_Rule_Definition('FWS')),
								new Phools_Parser_Rule_Definition('VCHAR')))),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('WSP')),
				)));

		//   date-time       =   [ day-of-week "," ] date time [CFWS]
		$this->define('date-time', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Definition('day-of-week'),
								new Phools_Parser_Rule_Char(','),
						))),
				new Phools_Parser_Rule_Definition('date'),
				new Phools_Parser_Rule_Definition('time'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
		)));

		//   day-of-week     =   ([FWS] day-name) / obs-day-of-week
		$this->define('day-of-week', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('FWS')),
				new Phools_Parser_Rule_Definition('day-name'),
		)));

		//   day-name        =   "Mon" / "Tue" / "Wed" / "Thu" /
		//                       "Fri" / "Sat" / "Sun"
		$this->define('day-name', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Keyword('Mon'),
				new Phools_Parser_Rule_Keyword('Tue'),
				new Phools_Parser_Rule_Keyword('Wed'),
				new Phools_Parser_Rule_Keyword('Thu'),
				new Phools_Parser_Rule_Keyword('Fri'),
				new Phools_Parser_Rule_Keyword('Sat'),
				new Phools_Parser_Rule_Keyword('Sun'),
		)));

		//   date            =   day month year
		$this->define('date', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('day'),
				new Phools_Parser_Rule_Definition('month'),
				new Phools_Parser_Rule_Definition('year'),
		)));

		//   day             =   ([FWS] 1*2DIGIT FWS) / obs-day
		$this->define('day', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('FWS')),
				new Phools_Parser_Rule_Repetition(1, 2,
						new Phools_Parser_Rule_Definition('DIGIT')),
				new Phools_Parser_Rule_Definition('FWS'),
		)));


		//   month           =   "Jan" / "Feb" / "Mar" / "Apr" /
		//                       "May" / "Jun" / "Jul" / "Aug" /
		//                       "Sep" / "Oct" / "Nov" / "Dec"
		$this->define('month', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Keyword('Jan'),
				new Phools_Parser_Rule_Keyword('Feb'),
				new Phools_Parser_Rule_Keyword('Mar'),
				new Phools_Parser_Rule_Keyword('Apr'),
				new Phools_Parser_Rule_Keyword('May'),
				new Phools_Parser_Rule_Keyword('Jun'),
				new Phools_Parser_Rule_Keyword('Jul'),
				new Phools_Parser_Rule_Keyword('Aug'),
				new Phools_Parser_Rule_Keyword('Sep'),
				new Phools_Parser_Rule_Keyword('Oct'),
				new Phools_Parser_Rule_Keyword('Nov'),
				new Phools_Parser_Rule_Keyword('Dec'),
		)));

		//   year            =   (FWS 4*DIGIT FWS) / obs-year
		$this->define('year', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('FWS'),
				new Phools_Parser_Rule_Repetition(4, PHP_INT_SIZE,
						new Phools_Parser_Rule_Definition('DIGIT')),
				new Phools_Parser_Rule_Definition('FWS'),
		)));

		//   time            =   time-of-day zone
		$this->define('time', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('time-of-day'),
				new Phools_Parser_Rule_Definition('zone'),
		)));

		//   time-of-day     =   hour ":" minute [ ":" second ]
		$this->define('time-of-day', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('hour'),
				new Phools_Parser_Rule_Char(':'),
				new Phools_Parser_Rule_Definition('minute'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Char(':'),
								new Phools_Parser_Rule_Definition('second'),
						))),
		)));

		//   hour            =   2DIGIT / obs-hour
		$this->define('hour', new Phools_Parser_Rule_Repetition(2, 2,
				new Phools_Parser_Rule_Definition('DIGIT')));

		//   minute          =   2DIGIT / obs-minute
		$this->define('minute', new Phools_Parser_Rule_Repetition(2, 2,
				new Phools_Parser_Rule_Definition('DIGIT')));

		//   second          =   2DIGIT / obs-second
		$this->define('second', new Phools_Parser_Rule_Repetition(2, 2,
				new Phools_Parser_Rule_Definition('DIGIT')));

		//   zone            =   (FWS ( "+" / "-" ) 4DIGIT) / obs-zone
		$this->define('zone', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('FWS'),
				new Phools_Parser_Rule_Alternative(array(
						new Phools_Parser_Rule_Char('+'),
						new Phools_Parser_Rule_Char('-'),
				)),
				new Phools_Parser_Rule_Repetition(4, 4,
						new Phools_Parser_Rule_Definition('DIGIT')),
		)));

		//   address         =   mailbox / group
		$this->define('address', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('mailbox'),
				new Phools_Parser_Rule_Definition('group'),
		)));

		//   mailbox         =   name-addr / addr-spec
		$this->define('mailbox', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('name-addr'),
				new Phools_Parser_Rule_Definition('addr-spec'),
		)));

		//   name-addr       =   [display-name] angle-addr
		$this->define('name-addr', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('display-name')),
				new Phools_Parser_Rule_Definition('angle-addr'),
		)));

		//   angle-addr      =   [CFWS] "<" addr-spec ">" [CFWS] / obs-angle-addr
		$this->define('angle-addr', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
				new Phools_Parser_Rule_Char('<'),
				new Phools_Parser_Rule_Definition('addr-spec'),
				new Phools_Parser_Rule_Char('>'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS'))
		)));

		//   group           =   display-name ":" [group-list] ";" [CFWS]
		$this->define('group', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('display-name'),
				new Phools_Parser_Rule_Char(':'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('group-list')),
				new Phools_Parser_Rule_Char(';'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
		)));

		//   display-name    =   phrase
		$this->define('display-name', new Phools_Parser_Rule_Definition('phrase'));

		//   mailbox-list    =   (mailbox *("," mailbox)) / obs-mbox-list
		$this->define('mailbox-list', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('mailbox'),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Char(','),
								new Phools_Parser_Rule_Definition('mailbox'),
						))),
		)));

		//   address-list    =   (address *("," address)) / obs-addr-list
		$this->define('address-list', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('address'),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Char(','),
								new Phools_Parser_Rule_Definition('address'),
						))),
		)));

		//   group-list      =   mailbox-list / CFWS / obs-group-list
		$this->define('group-list', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('mailbox-list'),
				new Phools_Parser_Rule_Definition('CFWS'),
		)));

		//    addr-spec       =   local-part "@" domain
		$this->define('addr-spec', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('local-part'),
				new Phools_Parser_Rule_Char('@'),
				new Phools_Parser_Rule_Definition('domain'),
		)));

		//   local-part      =   dot-atom / quoted-string / obs-local-part
		$this->define('local-part', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('dot-atom'),
				new Phools_Parser_Rule_Definition('quoted-string'),
		)));

		//   domain          =   dot-atom / domain-literal / obs-domain
		$this->define('domain', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('dot-atom'),
				new Phools_Parser_Rule_Definition('domain-literal'),
		)));

		//   domain-literal  =   [CFWS] "[" *([FWS] dtext) [FWS] "]" [CFWS]
		$this->define('domain-literal', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
				new Phools_Parser_Rule_Char('['),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Repetition(0, 1,
										new Phools_Parser_Rule_Definition('FWS')),
								new Phools_Parser_Rule_Definition('dtext'),
						))),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('FWS')),
				new Phools_Parser_Rule_Char(']'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
		)));

		//   dtext           =   %d33-90 /          ; Printable US-ASCII
		//                       %d94-126 /         ;  characters not including
		//                       obs-dtext          ;  "[", "]", or "\"
		$this->define('dtext', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_AsciiRange(33, 90),
				new Phools_Parser_Rule_AsciiRange(94, 126),
		)));

		//   orig-date       =   "Date:" date-time CRLF
		$this->define('orig-date', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Date:'),
				new Phools_Parser_Rule_Definition('date-time'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   from            =   "From:" mailbox-list CRLF
		$this->define('from', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('From:'),
				new Phools_Parser_Rule_Definition('mailbox-list'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   sender          =   "Sender:" mailbox CRLF
		$this->define('sender', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Sender:'),
				new Phools_Parser_Rule_Definition('mailbox'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   reply-to        =   "Reply-To:" address-list CRLF
		$this->define('reply-to', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Reply-To:'),
				new Phools_Parser_Rule_Definition('address-list'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   to              =   "To:" address-list CRLF
		$this->define('to', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('To:'),
				new Phools_Parser_Rule_Definition('address-list'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   cc              =   "Cc:" address-list CRLF
		$this->define('cc', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Cc:'),
				new Phools_Parser_Rule_Definition('address-list'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   bcc             =   "Bcc:" [address-list / CFWS] CRLF
		$this->define('bcc', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Bcc:'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Alternative(array(
								new Phools_Parser_Rule_Definition('address-list'),
								new Phools_Parser_Rule_Definition('CFWS')))),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   message-id      =   "Message-ID:" msg-id CRLF
		$this->define('message-id', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Message-ID:'),
				new Phools_Parser_Rule_Definition('msg-id'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   in-reply-to     =   "In-Reply-To:" 1*msg-id CRLF
		$this->define('in-reply-to', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('In-Reply-To:'),
				new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('msg-id')),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   references      =   "References:" 1*msg-id CRLF
		$this->define('references', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('References:'),
				new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('msg-id')),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   msg-id          =   [CFWS] "<" id-left "@" id-right ">" [CFWS]
		$this->define('msg-id', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
				new Phools_Parser_Rule_Char('<'),
				new Phools_Parser_Rule_Definition('id-left'),
				new Phools_Parser_Rule_Char('@'),
				new Phools_Parser_Rule_Definition('id-right'),
				new Phools_Parser_Rule_Char('>'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('CFWS')),
		)));

		//   id-left         =   dot-atom-text / obs-id-left
		$this->define('id-left', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('dot-atom-text'),
		)));

		//   id-right        =   dot-atom-text / no-fold-literal / obs-id-right
		$this->define('id-right', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('dot-atom-text'),
				new Phools_Parser_Rule_Definition('no-fold-literal'),
		)));

		//   no-fold-literal =   "[" *dtext "]"
		$this->define('no-fold-literal', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Char('['),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('dtext')),
				new Phools_Parser_Rule_Char(']'),
		)));

		//   subject         =   "Subject:" unstructured CRLF
		$this->define('subject', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Subject:'),
				new Phools_Parser_Rule_Definition('unstructured'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   comments        =   "Comments:" unstructured CRLF
		$this->define('comments', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Comments:'),
				new Phools_Parser_Rule_Definition('unstructured'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   keywords        =   "Keywords:" phrase *("," phrase) CRLF
		$this->define('keywords', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Keywords:'),
				new Phools_Parser_Rule_Definition('phrase'),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Char(','),
								new Phools_Parser_Rule_Definition('phrase'),
						))),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//    resent-date     =   "Resent-Date:" date-time CRLF
		$this->define('resent-date', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Resent-Date:'),
				new Phools_Parser_Rule_Definition('date-time'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//    resent-from     =   "Resent-From:" mailbox-list CRLF
		$this->define('resent-from', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Resent-From:'),
				new Phools_Parser_Rule_Definition('mailbox-list'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//    resent-sender   =   "Resent-Sender:" mailbox CRLF
		$this->define('resent-sender', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Resent-Sender:'),
				new Phools_Parser_Rule_Definition('mailbox'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//    resent-to       =   "Resent-To:" address-list CRLF
		$this->define('resent-to', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Resent-To:'),
				new Phools_Parser_Rule_Definition('address-list'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//    resent-cc       =   "Resent-Cc:" address-list CRLF
		$this->define('resent-cc', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Resent-Cc:'),
				new Phools_Parser_Rule_Definition('address-list'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//    resent-bcc      =   "Resent-Bcc:" [address-list / CFWS] CRLF
		$this->define('resent-bcc', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Resent-Bcc:'),
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Alternative(array(
								new Phools_Parser_Rule_Definition('address-list'),
								new Phools_Parser_Rule_Definition('CFWS'),
						))),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//    resent-msg-id   =   "Resent-Message-ID:" msg-id CRLF
		$this->define('resent-msg-id', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Resent-Message-ID:'),
				new Phools_Parser_Rule_Definition('msg-id'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   trace           =   [return]
		//                       1*received
		$this->define('trace', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, 1,
						new Phools_Parser_Rule_Definition('return')),
				new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('received')),
		)));

		//   return          =   "Return-Path:" path CRLF
		$this->define('return', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Return-Path:'),
				new Phools_Parser_Rule_Definition('path'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   path            =   angle-addr / ([CFWS] "<" [CFWS] ">" [CFWS])
		$this->define('path', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('angle-addr'),
				new Phools_Parser_Rule_Sequence(array(
						new Phools_Parser_Rule_Repetition(0, 1,
								new Phools_Parser_Rule_Definition('CFWS')),
						new Phools_Parser_Rule_Char('<'),
						new Phools_Parser_Rule_Repetition(0, 1,
								new Phools_Parser_Rule_Definition('CFWS')),
						new Phools_Parser_Rule_Char('>'),
						new Phools_Parser_Rule_Repetition(0, 1,
								new Phools_Parser_Rule_Definition('CFWS')),
				)),
		)));

		//   received        =   "Received:" *received-token ";" date-time CRLF
		$this->define('received', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Keyword('Received:'),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Definition('received-token')),
				new Phools_Parser_Rule_Char(';'),
				new Phools_Parser_Rule_Definition('date-time'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   received-token  =   word / angle-addr / addr-spec / domain
		$this->define('received-token', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Definition('word'),
				new Phools_Parser_Rule_Definition('angle-addr'),
				new Phools_Parser_Rule_Definition('addr-spec'),
				new Phools_Parser_Rule_Definition('domain'),
		)));

		//   optional-field  =   field-name ":" unstructured CRLF
		$this->define('optional-field', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('field-name'),
				new Phools_Parser_Rule_Char(':'),
				new Phools_Parser_Rule_Definition('unstructured'),
				new Phools_Parser_Rule_Definition('CRLF'),
		)));

		//   field-name      =   1*ftext
		$this->define('field-name', new Phools_Parser_Rule_Repetition(1, PHP_INT_MAX,
				new Phools_Parser_Rule_Definition('ftext')));

		//   ftext           =   %d33-57 /          ; Printable US-ASCII
		//                       %d59-126           ;  characters not including
		//                                          ;  ":".
		$this->define('ftext', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_AsciiRange(33, 57),
				new Phools_Parser_Rule_AsciiRange(59, 126),
		)));

// 	fields          =   *(trace
// 	                         *optional-field /
// 	                         *(resent-date /
// 	                          resent-from /
// 	                          resent-sender /
// 	                          resent-to /
// 	                          resent-cc /
// 	                          resent-bcc /
// 	                          resent-msg-id))
// 	                       *(orig-date /
// 	                       from /
// 	                       sender /
// 	                       reply-to /
// 	                       to /
// 	                       cc /
// 	                       bcc /
// 	                       message-id /
// 	                       in-reply-to /
// 	                       references /
// 	                       subject /
// 	                       comments /
// 	                       keywords /
// 	                       optional-field)
		$this->define('fields', new Phools_Parser_Rule_Alternative(array(
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Alternative(array(
							new Phools_Parser_Rule_Definition('orig-date'),
							new Phools_Parser_Rule_Definition('from'),
							new Phools_Parser_Rule_Definition('sender'),
							new Phools_Parser_Rule_Definition('reply-to'),
							new Phools_Parser_Rule_Definition('to'),
							new Phools_Parser_Rule_Definition('cc'),
							new Phools_Parser_Rule_Definition('bcc'),
							new Phools_Parser_Rule_Definition('message-id'),
							new Phools_Parser_Rule_Definition('in-reply-to'),
							new Phools_Parser_Rule_Definition('references'),
							new Phools_Parser_Rule_Definition('subject'),
							new Phools_Parser_Rule_Definition('comments'),
							new Phools_Parser_Rule_Definition('keywords'),
							new Phools_Parser_Rule_Definition('optional-field')))),
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Definition('trace'),
								new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
										new Phools_Parser_Rule_Definition('optional-field')),
								new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
										new Phools_Parser_Rule_Alternative(array(
												new Phools_Parser_Rule_Definition('resent-date'),
												new Phools_Parser_Rule_Definition('resent-from'),
												new Phools_Parser_Rule_Definition('resent-sender'),
												new Phools_Parser_Rule_Definition('resent-to'),
												new Phools_Parser_Rule_Definition('resent-cc'),
												new Phools_Parser_Rule_Definition('resent-bcc'),
												new Phools_Parser_Rule_Definition('resent-msg-id')))),
								))),
		)));

//   message         =   (fields / obs-fields)
//                       [CRLF body]
		$this->define('message', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Definition('fields'),
				new Phools_Parser_Rule_Definition('CRLF'),
				new Phools_Parser_Rule_Definition('body'),
		)));

//  body            =   (*(*998text CRLF) *998text) / obs-body
		$this->define('body', new Phools_Parser_Rule_Sequence(array(
				new Phools_Parser_Rule_Repetition(0, PHP_INT_MAX,
						new Phools_Parser_Rule_Sequence(array(
								new Phools_Parser_Rule_Repetition(0, 998,
										new Phools_Parser_Rule_Definition('text')),
								new Phools_Parser_Rule_Definition('CRLF')))),
						new Phools_Parser_Rule_Repetition(0, 998,
								new Phools_Parser_Rule_Definition('text')),
				)));

//   text            =   %d1-9 /            ; Characters excluding CR
//                       %d11 /             ;  and LF
//                       %d12 /
//                       %d14-127
		$this->define('text', new Phools_Parser_Rule_Alternative(array(
			new Phools_Parser_Rule_AsciiRange(1, 9),
			new Phools_Parser_Rule_AsciiRange(11),
			new Phools_Parser_Rule_AsciiRange(12),
			new Phools_Parser_Rule_AsciiRange(14, 127),
			)));
	}

}
