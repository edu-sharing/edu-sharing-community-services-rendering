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

class mc_ErrorHandler
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
	var $mail_target="info@edu-sharing.net";
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

	/**
	* constructor
	*/
	public function __construct($p_error_id = 0)
	{
		return;
	} // end constructor


	public function handleError($p_debug, $p_err_item, $p_err_msg)
	{
		if ( mc_Debug::check($p_debug) == false )
		{
			return;
		}

		$l_buffer = @ob_get_clean();

		$l_backtrace = debug_backtrace();
		$l_backtrace = array_reverse($l_backtrace);
		$l_default_stage = array(
			'class'  => '',
			'type'   => '',
			'object' => '',
			'args'   => '',
		);
		$l_temp = '';
		$l_list = '';

		foreach ($l_backtrace as $l_stage)
		{
			if ($l_stage['function'] === 'handleError')
			{
				continue;
			}

			$l_row = array_merge($l_default_stage, $l_stage);

			$l_temp .= '
<table style="font-size:.8em;">
<tr><td valign="top">location : </td><td valign="top">line <b>'.$l_row['line'].'</b> in file <b>'.$l_row['file'].'</b></td></tr>
<tr><td valign="top">calling : </td><td valign="top">'.$l_row['class'].$l_row['type'].$l_row['function'].'</td></tr>';
			if ($l_row['function'] == 'error')
			{
				$l_temp .= '
<tr><td valign="top">with error : </td><td valign="top">{PARSED_ERROR}</td></tr>
</table>';
			}
			else
			{
				$l_list[] = $l_row['file'].':'.$l_row['line'].' ('.$l_row['class'].$l_row['type'].$l_row['function'].')';
				$l_temp .= '
<tr><td valign="top">with arguments : </td><td valign="top"><pre>'.htmlentities(print_r($l_row['args'], true)).'</pre></td></tr>
</table>
<hr />';
			}
		}

		$l_location = '
	executing file <b>'.str_replace(MC_ROOT_PATH, MC_ROOT_URI, $l_row['file']).'</b>, line <b>'.$l_row['line'].'</b>';


		$l_backtrace = '<p>'.$l_temp.'</p>';

		$l_msg = '';

		if (empty($p_err_msg))
		{
			$l_msg .= '<div class="err_empty">no query/msg details given: (parameter is empty)</div>';
		}
		else
		{
			$l_msg .= '<b>QUERY/MSG:</b><div class="err_debug">'.$p_err_msg.'</div>';
		}

		if (is_object($p_err_item))
		{
			if (method_exists($p_err_item, "getDebugInfo"))
			{
				if ($p_err_item->getDebugInfo() != $p_err_msg)
				{
					$l_msg .= '<b>RESULT=> DEBUG_INFO:</b><div class="err_debug">'.$p_err_item->getDebugInfo().'</div>';
				}
			}
/*
			// content of user info is similar to content of debug info
			if (method_exists($p_err_item, "getUserInfo"))
			{
				$l_msg .= '<b>RESULT=> USER_INFO:</b><div class="err_user">'.$p_err_item->getUserInfo().'</div>';
			}
*/
			if (method_exists($p_err_item, "getMessage"))
			{
				$l_msg .= '<b>RESULT=> MESSAGE:</b><div class="err_msg">'.$p_err_item->getMessage().'</div>';
			}

			if (method_exists($p_err_item, "getCode"))
			{
				$l_msg .= '<b>RESULT=> CODE:</b> '.$p_err_item->getCode().'<br />';
			}
		}

		if ($p_err_item === null)
		{
			$l_msg .= '<div style="err_empty">no error details given: (parameter is <b>NULL</b>)</div><div id="errdump" class="err_dump">'.$l_backtrace.'</div>';
			$l_parsed_error = 'NULL';
		}
		else
		{
			$l_msg .= '<b>RESULT=> <a href="#" onClick="var ed=document.getElementById(\'errdump\').style;if(ed.display==\'none\'){ed.display=\'block\';firstChild.data=\'hide DUMP\';}else{ed.display=\'none\';firstChild.data=\'show DUMP\';}">show DUMP</a></b><div id="errdump" style="display:none;" class="err_dump">'.$l_backtrace.'</div>';

			ob_start();
			var_dump($p_err_item);
			$l_err_dump = ob_get_clean();

			$l_parsed_error = '<pre>'.$l_err_dump.'</pre>';
		}

		echo '
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
div.err_dump pre {
	font-size:12px;
}
</style>
<div class="err_all">
	<div class="err_title">
		Error in file <b>'.str_replace(MC_ROOT_PATH, MC_ROOT_URI, MC_DOCROOT.$_SERVER["PHP_SELF"]).'</b><p />
'.$l_location.'<p />
  	buffer : '.(($l_buffer === false) ? "NOT active" : "ACTIVE").'
	</div>
	<div style="margin-top:0px;">
	BACKTRACE :<br>
'.implode("<br>\n", array_reverse($l_list)).'
	</div>
	<hr />
'.str_replace('{PARSED_ERROR}', $l_parsed_error, $l_msg).'
</div>
';

		switch(true)
		{
			case ($l_buffer === false) :
				// no buffer at all
				break;

			case (empty($l_buffer)) :
				// buffer running but empty
				ob_start();
				break;

			default :
				// buffer running and with content
				echo '<hr>'
					.'buffer content:'
					.'<div style="width:98%;height:200px;overflow:scroll;background-color:#F0F0F0;border:2px inset black;">'
					.nl2br(htmlentities($l_buffer))
					.'</div>'
				;
				ob_start();
				echo $l_buffer;
				break;
		}

		if (MC_DIE_ON_ERROR == true)
		{
			die();
		}

		return;
	} // end method handleError


} // end class mc_ErrorHandler

?>