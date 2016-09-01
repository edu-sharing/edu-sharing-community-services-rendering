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

if (defined('MC_ROOT_PATH') == false)
{
/*
	if (file_exists("../dblog.inc.php"))
	{
		include_once("../dblog.inc.php");
	}
	else if (file_exists("../../dblog.inc.php"))
	{
		include_once("../../dblog.inc.php");
	}
	else if (file_exists("../../../dblog.inc.php"))
	{
		include_once("../../../dblog.inc.php");
	}
*/
	die('missing constant MC_ROOT_PATH in file '.__FILE__.' at line '.__LINE__);
}


// BEGIN class Error
class Error
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

	// fehlermasken zur filterung (addition obenstehender fehlerkonstanten oder 0 für "keine reaktion")
	// werden im konstruktor eingestellt
	var $LOG_DB_ERROR_MASK=0;
	var $LOG_FILE_ERROR_MASK=0;
	var $SEND_MAIL_ERROR_MASK=0;

	// weitere einstellungen
	var $use_date_prefix=true;	// benutzt datum (dd-mm-yyyy) als primären präfix des log-datei-names
	var $use_session_prefix=true;	// benutzt session_id (wenn vorhanden) als sekundären präfix des log-datei-names
	var $mail_target="info@edu-sharing.net";
	var $LOG_SCRIPT_CODE=TRUE;	// beim logging die fragliche codezeile direkt mit einbinden : on / off

	var $DIE_ON_ERROR=FALSE;

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


	/**
	* leeres fehlerobjekt erzeugen (keine parameterübergabe) oder fehler aus datenbank einlesen (übergabe von id)
	*/
	function Error($p_error_id=0)
	{

		// E_ERROR+E_WARNING+E_PARSE+E_NOTICE+E_CORE_ERROR+E_CORE_WARNING+E_COMPILE_ERROR+E_COMPILE_WARNING+E_USER_ERROR+E_USER_WARNING+E_USER_NOTICE;
		$this->LOG_DB_ERROR_MASK    = E_ERROR + E_WARNING + E_USER_ERROR + E_USER_WARNING + E_USER_NOTICE;
		$this->LOG_FILE_ERROR_MASK  = E_ERROR + E_WARNING + E_USER_ERROR + E_USER_WARNING + E_USER_NOTICE;
		$this->SEND_MAIL_ERROR_MASK = 0;

/*
		if (is_int($p_error_id) && $p_error_id) {
			// möglicherweise fehler-ID aus datenbank -> fehler einlesen
			$this->readDBLog($p_error_id);
		}
		// else echo "leeres fehlerobjekt erzeugt";
*/

		return;
	} // Ende Konstruktor


	function catchError($p_err_type, $p_err_msg, $p_file, $p_line, $p_err_context, $p_handleError=TRUE)
	{
		GLOBAL $SID;

		// ggf. fehlerobjekt objekt zerlegen
		if (is_object($p_err_msg)) $this->msg_data=get_object_vars($p_err_msg);
		// ggf. fehlertyp aus objektdaten ermitteln
		if ($p_err_type==0 && $this->msg_data['level']) $p_err_type=$this->msg_data['level'];

		$this->ERR_SID		=(isSet($SID)) ? $SID : "";
		$this->ERR_APP_IDENT	=(isSet($_SESSION['APP_IDENT']))  ? $_SESSION['APP_IDENT']  : -1;
		$this->ERR_COURSE_ID	=(isSet($_SESSION['COURSE_ID']))  ? $_SESSION['COURSE_ID']  : -1;
		$this->ERR_USER_IDENT	=(isSet($_SESSION['USER_IDENT'])) ? $_SESSION['USER_IDENT'] : -1;
		$this->ERR_SCRIPT_PATH	=dirname($p_file);
		$this->ERR_SCRIPT_NAME	=basename($p_file);
		$this->ERR_SCRIPT_LINE	=$p_line;
		$this->ERR_TYPE		=$p_err_type;
		$this->ERR_MSG		=$p_err_msg;
		$this->ERR_CONTEXT	=$p_err_context;
		$this->ERR_TIMESTAMP	=time();

		$this->ERR_NAME=$this->TYPE[$p_err_type];

		if ($p_handleError) return $this->handleMcError();
		return true;
	}

	function handleMcError()
	{
		GLOBAL $MC_DEBUG_MODE, $MC_USER_ERR_MASK;

		// wurde ein Fehler definiert/aufgefangen ?
		if (!$this->ERR_TIMESTAMP) return false;

		// cancel reporting to pre-undefined arrays
		//if ($p_err_type == E_NOTICE && substr($p_err_msg, 0, 17) == "Undefined index: ") return;

		// fehlertyp-abhängiges verhalten festlegen!
		switch($this->ERR_TYPE) {
			case E_ERROR		:
			case E_WARNING		:
			case E_PARSE		:
			case E_NOTICE		:
			case E_CORE_ERROR	:
			case E_CORE_WARNING	:
			case E_COMPILE_ERROR	:
			case E_COMPILE_WARNING	:
			case E_USER_ERROR	:
			case E_USER_WARNING	:
			case E_USER_NOTICE	:
			default			:	// fehler in datenbank loggen ?
							if ($this->LOG_DB_ERROR_MASK & $this->ERR_TYPE) $this->writeDBLog();
							// fehler in datei loggen ?
							if ($this->LOG_FILE_ERROR_MASK & $this->ERR_TYPE) $this->writeFileLog();
							// mail an definierte adresse senden ?
							if ($this->SEND_MAIL_ERROR_MASK & $this->ERR_TYPE) $this->sendMail($this->mail_target);
							// meldung an benutzer machen ?
							if ($MC_DEBUG_MODE==false && ($MC_USER_ERR_MASK & $this->ERR_TYPE)) echo $this->getUserMsg();
							// debugging on --> ausführliche meldung
							if ($MC_DEBUG_MODE==true) die($this->getDevMsg());
							die("");
		}

		return true;
	}



	function writeDBLog()
	{
		global $dbhost, $dbuser, $pwd, $db;

		$connect=mysql_connect("$dbhost", "$dbuser", "$pwd") or die ("mysql_connect error (Error::writeDBLog())");
		$selDB=mysql_select_db("$db",$connect) or die ("mysql_select_db error (Error::writeDBLog())");

		$select = "insert into ERRORLOG";
		$select.=" set";
		$select.=" ERRORLOG_SESSION_SID  ='".$this->ERR_SID."',";
		$select.=" ERRORLOG_APP_IDENT    ='".$this->ERR_APP_IDENT."',";
		$select.=" ERRORLOG_COURSE_ID    ='".$this->ERR_COURSE_ID."',";
		$select.=" ERRORLOG_USER_IDENT   ='".$this->ERR_USER_IDENT."',";
		$select.=" ERRORLOG_SCRIPT_NAME  ='".$this->ERR_SCRIPT_NAME."',";
		$select.=" ERRORLOG_SCRIPT_PATH  ='".$this->ERR_SCRIPT_PATH."',";
		$select.=" ERRORLOG_SCRIPT_LINE  ='".$this->ERR_SCRIPT_LINE."',";
		$select.=" ERRORLOG_ERROR_TYPE   ='".$this->ERR_TYPE."',";
		$select.=" ERRORLOG_ERROR_MSG    ='".addslashes(serialize($this->ERR_MSG))."',";
		$select.=" ERRORLOG_ERROR_CONTEXT='".addslashes(serialize($this->ERR_CONT))."',";
		$select.=" ERRORLOG_TIMESTAMP    =". $this->ERR_TIMESTAMP;

		// fehler in datenbank schreiben
		$result=mysql_query($select) or die ("mysql_query error (Error::writeDBLog(); select: $select)");

		// fehlernummer zurückgeben
		$select="select LAST_INSERT_ID() from ERRORLOG";
		$result=mysql_query($select) or die ("mysql_query error (Error::writeDBLog(); select: $select)");
		$row=mysql_fetch_row($result);
		$this->ERR_ID=$row[0];
		return true;
	}


	function writeFileLog ($p_logpath="")
	{
		GLOBAL $ROOT_PATH;

		// filename für logfile erzeugen
		if ($p_logpath=="") $p_logpath=MC_ROOT_PATH."error/errorlogs/";
		if ($this->use_date_prefix) $datepref=date("d-m-Y", $this->ERR_TIMESTAMP).".";
		else $datepref="";
		if ($this->use_session_prefix && $this->ERR_SID!="") $sesspref=$this->ERR_SID.".";
		else $sesspref="";
		$filename=$p_logpath.$datepref.$sesspref."errorlog.html";

		if (@touch($filename)) {
			error_log ($this->getDevMsg(), 3, $filename);
			return true;
		}
		return false;
	}


	function sendMail ($p_mail_address)
	{
		// mail mit developer-log-nachricht an adresse $p_mail_address schicken
		//$l_mail_header="Subject: abhängen\nFrom: lust@habich.net\n";

		$l_mail_header ="Subject: ".$this->ERR_NAME." - ".$this->ERR_SCRIPT_NAME."\n";
		$l_mail_header.="MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\n";
		$l_mail_msg ="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 3.2//EN\">";
		$l_mail_msg.="<head><title> DEBUG message</title></head>";
		$l_mail_msg.="<HTML><BODY>".$this->getDevMsg()."</BODY></HTML>";

		error_log ($l_mail_msg, 1, $p_mail_address, $l_mail_header);
	}


	function getUserMsg()
	{
		// fehlertyp ausgeben
		$usermsg="<table border=1></tr><td style='font-size:10pt;color:#FF0000;'>".$this->ERR_NAME;

		// fehlercode (id in errorlog db) ausgeben
		if ($this->ERR_ID)
			$usermsg.="<br />(Fehler-Code: <b>".$this->ERR_ID."</b> )";

		// nachricht ausgeben
		if ($this->msg_data['error_message_prefix'])
			$usermsg.="</td></tr><tr><td>".$this->msg_data['error_message_prefix'];

		$usermsg.="</td></tr></table>";

		return $usermsg;
	}


	function getDevMsg()
	{
		if ($this->ERR_ID) $id.=$this->ERR_ID;
		else $id="not logged";

		$htmlmsg="
<table border=1>
  <tr>
    <td style='background-color:#D8FFD8;'><b>Zeit:  </b>".
    date("H:i:s",$this->ERR_TIMESTAMP)." Uhr, ".date("d.m.Y",$this->ERR_TIMESTAMP)."</td>
    <td style='background-color:#FFE0D8;'><b>Type:  </b>".$this->ERR_NAME."</td>
    <td style='background-color:#FFE0D8;'><b>ErrID: </b>".$id."</td>
  </tr><tr>
    <td style='background-color:#D8FFD8;'><b>App:   </b>".$this->ERR_APP_IDENT."</td>
    <td style='background-color:#D8FFD8;'><b>Kurs:  </b>".$this->ERR_COURSE_ID."</td>
    <td style='background-color:#D8FFD8;'><b>User:  </b>".$this->ERR_USER_IDENT."</td>
  </tr><tr>
    <td colspan=3 style='padding:5pt;'>
    in Datei: <b>".$this->ERR_SCRIPT_PATH."/".$this->ERR_SCRIPT_NAME."</b><br>";
    		if ($this->LOG_SCRIPT_CODE) {
    			$htmlmsg.="
    in Zeile: <br /><b>".$this->ERR_SCRIPT_LINE."</b></b> &nbsp; ".$this->getScriptLine();
		}
		else {
			$htmlmsg.="
    in Zeile: <b>".$this->ERR_SCRIPT_LINE."</b>";
    		}
		$htmlmsg.="</td>
  </tr><tr>
    <td colspan=3><b>Fehler-Nachricht:</b><br>".$this->listMsgData($this->ERR_MSG)."</td>
  </tr><tr>
    <td colspan=3><b>Fehler-Kontext:</b><br>".$this->listContextData($this->ERR_CONTEXT)."</td>
  </tr>
</table><p />";
		return $htmlmsg;
	}


	function listContextData($p_context)
	{
		// parse context and add/extract additional information
		if (is_object($p_context)) $p_context=implode("<br>",get_object_vars($p_context));
		if (is_array($p_context)) $p_context=implode("<br>",$p_context);

		return $p_context;
	}


	function listMsgData($p_msg)
	{
		// parse msg and add/extract additional information
		if (sizeof($this->msg_data)) {
			$l_tmp=$this->msg_data;
			$c[0]="background-color:#FFFFFF;"; $c[1]="background-color:#EEEEFF;"; $i=0;
			$l_msg="<table style='border-spacing:5pt;'>";
			while($l_row=each($l_tmp)) {
				$l_msg.="<tr><td style='vertical-align:top;".$c[($i)]."'><b>".$l_row[0]."</b></td>";
				$l_msg.="<td style='vertical-align:top;".$c[($i)]."'>".$l_row[1]."</td></tr>";
				$i=1-$i;
			}
			$l_msg.="</table>";
			return $l_msg;
		}
		return $p_msg;
	}


	function getScriptLine ()
	{
		if ($log_buffer = file($this->ERR_SCRIPT_PATH."/".$this->ERR_SCRIPT_NAME)) {
			$script_line= $log_buffer[$this->ERR_SCRIPT_LINE-1];
			return ($script_line);
		}
		return "";
	}

	function readDBLog ($id=0)
	{
		global $dbhost, $dbuser, $pwd, $db;

		if (!$id) return;
		$connect=mysql_connect("$dbhost", "$dbuser", "$pwd") or die ("mysql_connect error (Error::readDBLog())");
		$selDB=mysql_select_db("$db",$connect) or die ("mysql_select_db error (Error::readDBLog())");

		$select = "select";
		$select.=" ERRORLOG_ID, ERRORLOG_SESSION_SID, ERRORLOG_APP_IDENT, ERRORLOG_COURSE_ID,";
		$select.=" ERRORLOG_USER_IDENT, ERRORLOG_SCRIPT_PATH, ERRORLOG_SCRIPT_NAME, ERRORLOG_SCRIPT_LINE,";
		$select.=" ERRORLOG_ERROR_TYPE, ERRORLOG_ERROR_MSG, ERRORLOG_ERROR_CONTEXT, ERRORLOG_TIMESTAMP";
		$select.=" from ERRORLOG";
		$select.=" where ERRORLOG_ID=".$id;

		// fehler in datenbank schreiben
		$result=mysql_query($select) or die ("mysql_query error (Error::readDBLog(); select: $select)");
		$error=mysql_fetch_array($result);

		$this->setErrorData($error);

		mysql_free_result($result);
		unset($error);
		return;
	}

	function setErrorData($p_errordata)
	{
		if (!is_array($p_errordata)) return false;
		$this->ERR_ID		=$p_errordata['ERRORLOG_ID'];
		$this->ERR_SID		=$p_errordata['ERRORLOG_SESSION_ID'];
		$this->ERR_APP_IDENT	=$p_errordata['ERRORLOG_APP_IDENT'];
		$this->ERR_COURSE_ID	=$p_errordata['ERRORLOG_COURSE_ID'];
		$this->ERR_USER_IDENT	=$p_errordata['ERRORLOG_USER_IDENT'];
		$this->ERR_SCRIPT_PATH	=$p_errordata['ERRORLOG_SCRIPT_PATH'];
		$this->ERR_SCRIPT_NAME	=$p_errordata['ERRORLOG_SCRIPT_NAME'];
		$this->ERR_SCRIPT_LINE	=$p_errordata['ERRORLOG_SCRIPT_LINE'];
		$this->ERR_TYPE		=$p_errordata['ERRORLOG_ERROR_TYPE'];
		$this->ERR_MSG		=unserialize($p_errordata['ERRORLOG_ERROR_MSG']);
		$this->ERR_CONTEXT	=unserialize($p_errordata['ERRORLOG_ERROR_CONTEXT']);
		$this->ERR_TIMESTAMP	=$p_errordata['ERRORLOG_TIMESTAMP'];
		$this->ERR_NAME		=$this->TYPE[$this->ERR_TYPE];

		// ggf. fehlerobjekt objekt zerlegen
		if (is_object($this->ERR_MSG)) $this->msg_data=get_object_vars($this->ERR_MSG);
		return true;
	}



	function handleError($p_debug, $p_err, $p_query, $p_file = null, $p_line = null)
	{

		if (function_exists('debug_backtrace'))
		{
			$l_backtrace = debug_backtrace();
			$l_backtrace = array_reverse($l_backtrace);
			$l_temp = '';
			$l_default = array(
				'class' => '',
				'type' => '',
				'object' => '',
				'args' => '',
			);

			foreach ($l_backtrace as $l_row)
			{
				$l_row = array_merge($l_default, $l_row);

				if ($l_row['function'] == 'handleerror')
				{
					continue;
				}

				$l_temp .= '
<table style="font-size:.8em;">
  <tr><td>location : </td><td>line <b>'.$l_row['line'].'</b> in file <b>'.$l_row['file'].'</b></td></tr>
  <tr><td>calling : </td><td>'.$l_row['class'].$l_row['type'].$l_row['function'].'</td></tr>
';
				if ($l_row['function'] == 'error')
				{
				$l_temp .= '
  <tr><td>with error : </td><td></td></tr>
</table>
';
				}
				else
				{
//  <tr><td valign="top">??? : </td><td><pre>'.htmlentities(print_r($l_row['object'], true)).'</pre></td></tr>
					$l_temp .= '
  <tr><td valign="top">with arguments : </td><td><pre>'.htmlentities(print_r($l_row['args'], true)).'</pre></td></tr>
</table>
<hr />
';
				}
			}

			$l_backtrace = '<p>'.$l_temp.'</p>';
		}
		else
		{
			$l_backtrace = '';
		}

		if ( Error::mcCheckDebug($p_debug) == false )
		{
			return;
		}

		$l_msg = '';

		if (empty($p_query))
		{
			$l_msg.= '<div style="err_empty">no query/msg details given: (parameter is empty)</div>';
		}
		else
		{
			$l_msg.= '<b>QUERY/MSG:</b><div class="err_debug">'.$p_query.'</div>';
		}

		if (method_exists($p_err,"getDebugInfo"))
		{
			if ($p_err->getDebugInfo() != $p_query)
			{
				$l_msg.= '<b>RESULT=> DEBUG_INFO:</b><div class="err_debug">'.$p_err->getDebugInfo().'</div>';
			}
		}
/*
		// content of user info is similar to content of debug info
		if (method_exists($p_err,"getUserInfo"))
		{
			$l_msg.= '<b>RESULT=> USER_INFO:</b><div class="err_user">'.$p_err->getUserInfo().'</div>';
		}
*/
		if (method_exists($p_err,"getMessage"))
		{
			$l_msg.= '<b>RESULT=> MESSAGE:</b><div class="err_msg">'.$p_err->getMessage().'</div>';
		}

		if (method_exists($p_err,"getCode"))
		{
			$l_msg.= '<b>RESULT=> CODE:</b> '.$p_err->getCode().'<br />';
		}

		if ($p_err === null)
		{
			$l_msg.= '<div style="err_empty">no result details given: (parameter is <b>NULL</b>)</div><div id="errdump" class="err_dump">'.$l_backtrace.'</div>';
		}
//		else if (!empty($p_err)) {
		else
		{
			$l_buff = ob_get_contents();
			if ($l_buff !== false)
			{
				ob_end_clean();
			}

			ob_start();
 				var_dump($p_err);
				$l_err_dump = ob_get_contents();
			ob_end_clean();
			$l_msg.= '<b>RESULT=> <a href="#" onClick="var ed=document.getElementById(\'errdump\').style;if(ed.display==\'none\'){ed.display=\'block\';firstChild.data=\'hide DUMP\';}else{ed.display=\'none\';firstChild.data=\'show DUMP\';}">show DUMP</a></b><div id="errdump" style="display:none;" class="err_dump">'.$l_backtrace.'<pre>'.$l_err_dump.'</pre></div>';
		}

/*
		if (isSet($this->PHP_SELF))
		{
			$l_self = $this->PHP_SELF;
		}
		else
		{
			$l_self = '';
		}
*/
		$l_self = $_SERVER['PHP_SELF'];

		$l_location = '';
		if ($p_file !== null)
		{
			$l_location .= '
	executing file <b>'.str_replace(MC_ROOT_PATH, MC_ROOT_URI, $p_file).'</b>';
		}

		if ($p_line !== null)
		{
			$l_location .= ', line <b>'.$p_line.'</b>';
		}

		$l_msg = '
<!-- /><!-- -->
<style type="text/css">
div {
	margin:5px;
	padding:5px;
	font-family:verdana,arial,helvetica,serif;
	font-size:11px;
}
div.err_all {
	border:solid 2px #0000A0;
}
div.err_title {
	color:#A00000;
}
div.err_debug, div.err_user, div.err_msg, div.err_dump {
	background-color:#E0E0E0;
}
div.err_dump {
	font-family:courier,helvetica,serif;
}
</style>
<div class="err_all">
<div class="err_title">
	Error in file <b>'.str_replace(MC_ROOT_PATH, MC_ROOT_URI, $_SERVER['DOCUMENT_ROOT'].$l_self).'</b><p />
'.$l_location.'
</div>
	<hr />
	'.$l_msg.'
</div>
';

		echo $l_msg;

		if ($this->DIE_ON_ERROR == true)
		{
			die();
		}

		if ($l_buff !== false)
		{
			ob_start();
			echo $l_buff;
		}

	}




	function mcCheckDebug(&$p_debug)
	{
		if ( empty($p_debug) )
		{
			return false;
		}

		if ($p_debug === true || empty($_SESSION['USER_IDENT']) )
		{
			$c = "ALL";
		}
		else
		{
			if ($_SESSION['USER_IDENT'] != $p_debug && $_SESSION['USER_IDENT'] != 1)
			{
				return false;
			}
			$c = "USER (IDENT:".$p_debug.")";
		}

		return '<span style="font-size:10px;font-weight:bold;color:#0000A0;font-family:verdana,arial,helvetica,courier">msg visible to: '.$c.'</span>';
	}


} // ENDE class Error




?>