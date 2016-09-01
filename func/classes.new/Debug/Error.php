<?php
/**
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


class mc_Error
{

/*
Wert	Konstante		Beschreibung			Bemerkung

1	E_ERROR			Fatale Laufzeit-Fehler. 	Zeigt nicht behebbare Fehler an (bspw. Probleme bei
								Speicherzuweisung). Ausführung des Skripts wird abgebrochen.
2	E_WARNING		Warnungen (no fatal errors)	Skript wird nicht abgebrochen.
				zur Laufzeit des Skripts.
4	E_PARSE			Parser-Fehler während		Parser-Fehler können nur vom Parser erzeugt werden.
				der Übersetzung.
8	E_NOTICE		Benachrichtigungen		Im Skript wurde irgend etwas gefunden, was einen Fehler
				während der Laufzeit. 		verursachen könnte. Möglicherweise aber auch reguläre
								Benachrichtigungen im ordnungsgemäßen Skriptablauf.
16	E_CORE_ERROR		Fatale Fehler, die beim 	Diese sind ähnlich wie E_ERROR, nur dass diese Fehler-
				Starten von PHP auftreten. 	meldungen vom PHP-Kern erzeugt werden. nur in PHP 4
32	E_CORE_WARNING	 	Warnungen (no fatal errors), 	Diese sind ähnlich wie E_WARNING, nur dass diese
				die beim PHP-Start auftreten. 	Warnungen vom PHP-Kern erzeugt werden. nur in PHP 4
64	E_COMPILE_ERROR		Fatale Fehler zur 		Diese sind ähnlich wie E_ERROR, allerdings von der
				Übersetzungszeit. 		Zend Scripting Engine erzeugt.         nur in PHP 4
128	E_COMPILE_WARNING	Warnungen zur 			Diese sind ähnlich wie E_WARNING, allerdings von der
				Übersetzungszeit. 		Zend Scripting Engine erzeugt.         nur in PHP 4
256	E_USER_ERROR		Benutzerdefinierte 		Diese sind ähnlich wie E_ERROR, werden jedoch im
				Fehlermeldungen. 		PHP-Code mit trigger_error() erzeugt.  nur in PHP 4
512	E_USER_WARNING		Benutzerdefinierte 		Diese sind ähnlich wie E_WARNING, werden jedoch im
				Warnungen.			PHP-Code mit trigger_error() erzeugt.  nur in PHP 4
1024	E_USER_NOTICE		Benutzerdefinierte 		Diese sind ähnlich wie E_NOTICE, werden jedoch im
				Benachrichtigung. 		PHP-Code mit trigger_error() erzeugt.  nur in PHP 4
-
2047	E_ALL			Alle Fehler und Warnungen die unterstützt werden, mit Ausnahme von E_STRICT.
-
2048	E_STRICT (integer) 	Benachrichtigungen des		Vorschläge von PHP für Änderungen des Programmcodes, um
				Laufzeitsystems.		bestmögliche Interoperabilität & zukünftige Kompatibilität
								des Codes zu gewährleisten.            nur in PHP 5
*/
/*
	// fehlermasken zur filterung (addition obenstehender fehlerkonstanten oder 0 für "keine reaktion")
	// werden im konstruktor eingestellt
	var $LOG_DB_ERROR_MASK=0;
	var $LOG_FILE_ERROR_MASK=0;
	var $SEND_MAIL_ERROR_MASK=0;

	// weitere einstellungen
	var $use_date_prefix=true;	// benutzt datum (dd-mm-yyyy) als primären präfix des log-datei-names
	var $use_session_prefix=true;	// benutzt session_id (wenn vorhanden) als sekundären präfix des log-datei-names
	var $mail_target="errormsg@metacoon.net";
	var $LOG_SCRIPT_CODE=TRUE;	// beim logging die fragliche codezeile direkt mit einbinden : on / off

	// private parameter
	var $ERR_ID		=0;
	var $ERR_SID		="";
	var $ERR_APP_IDENT	=-1;
	var $ERR_COURSE_ID	=-1;
	var $ERR_USER_IDENT	=-1;
	var $ERR_SCRIPT_PATH	="";
	var $ERR_SCRIPT_NAME	="";
	var $ERR_SCRIPT_LINE	=0;
	var $ERR_TYPE		=0;
	var $ERR_MSG		="";
	var $ERR_CONTEXT	="";
	var $ERR_TIMESTAMP	=0;
	var $ERR_NAME		="";
	var $msg_data;
	var $TYPE = array (
			1   =>  "System FATAL ERROR",
  		2   =>  "System ERROR",
			4   =>  "Parsing ERROR",
			8   =>  "System WARNING",
			16  =>  "Core Error",
			32  =>  "Core Warning",
			64  =>  "Compile Error",
			128 =>  "Compile Warning",
			256 =>  "User FATAL ERROR",
			512 =>  "User WARNING",
			1024=>  "User NOTICE",
			);
*/

	protected $level;

	protected $code;

	protected $debug_msg;

	protected $user_msg;

	/**
	 * constructor
	 */
	public function __construct($p_err_obj, $p_err_code, $p_debug_msg, $p_user_msg = null)
	{
		$this->level = E_USER_WARNING;

		$this->obj   = $p_err_obj;
		$this->code  = intval($p_err_code);
		$this->debug_msg = $p_debug_msg;
		$this->user_msg  = $p_user_msg;

		return;
	} // end constructor

	function setLevel($p_lvl)
	{
		$this->level = intval($p_lvl);
	}

	function getDebugInfo()
	{
		return $this->debug_msg;
	}

	function getMessage()
	{
		return $this->user_msg;
	}

	function getCode()
	{
		return $this->code;
	}


} // end class mc_Error




?>