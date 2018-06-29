<?php
/*
* $McLicense$
*
* $Id$
*
*/

/**
 * SysMsg
 *
 * @author [Autor]
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class SysMsg
{


  ////////////////////////////////////////////////////////////////////////////////////////////////
  //                                                                                            //
  //                   K O N S T R U K T O R  /  D E K O N S T R U K T O R                      //
  //                                                                                            //
  ////////////////////////////////////////////////////////////////////////////////////////////////


  /**
  * Konstruktor
  */
  function SysMsg()
  {
		return true;
  } // Ende Konstruktor



  ////////////////////////////////////////////////////////////////////////////////////////////////
  //                                                                                            //
  //                                    A L L G E M E I N E S                                   //
  //                                                                                            //
  ////////////////////////////////////////////////////////////////////////////////////////////////

  /**
  * Zeigt Nachrichten für den Benutzer an.
  *
  * Beispiele: "Wird geprüft/geladen/etc. ...", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Information
  */
  function showMsg($p_msg, $p_send_header = false)
  {
    return SysMsg::showMessage($p_msg, $p_send_header, false, 'msg');
  } // Ende Methode showMsg


  /**
  * Zeigt Informationen und Bestätigungen für den Benutzer an (grünes Feld mit grünem Ausrufezeichen).
  *
  * Beispiele: "Eintrag angelegt.", "Eintrag gelöscht.", "Suche beendet", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Information
  */
  function showInfo($p_msg, $p_send_header = false, $p_show_big = false)
  {
    return SysMsg::showMessage($p_msg, $p_send_header, $p_show_big, 'info');
  } // Ende Methode showInfo


  /**
  * Zeigt von Warnungen für den Benutzer an (rotes Feld mit rotem Ausrufezeichen).
  *
  * Beispiele: "Datei nicht gefunden.", "Löschen fehlgeschlagen.", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Warnung
  */
  function showWarning($p_msg, $p_send_header = false, $p_show_big = false)
  {
    return SysMsg::showMessage($p_msg, $p_send_header, $p_show_big, 'warning');
  } // Ende Methode showWarning



  /**
  * Zeigt Fehlermeldungen für den Benutzer an (wie Warnung).
  *
  * Beispiele: "Datei nicht gefunden.", "Löschen fehlgeschlagen.", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Warnung
  */
  function showError($p_msg, $p_send_header = false, $p_show_big = false)
  {
    return SysMsg::showMessage($p_msg, $p_send_header, $p_show_big, 'error');
  } // Ende Methode showError



  /**
  * Zeigt Aufgaben/Hinweise für den Benutzer/Administratoren an.
  *
  * Beispiele: "Sprache nicht gefunden. Bitte ergänzen Sie die Sprache in der Sprachtabelle.", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Warnung
  */
  function showTask($p_msg, $p_send_header = false, $p_show_big = false)
  {
    return SysMsg::showMessage($p_msg, $p_send_header, $p_show_big, 'task');
  } // Ende Methode showTask



  /**
  * Zeigt Hinweise/Beschreibungen an.
  *
  * Beispiele: "dies ist dazu da / beachten sie folgendes details / machen sie sich keine sorgen wenn...", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Warnung
  */
  function showNote($p_msg, $p_send_header = false, $p_show_big = false)
  {
    return SysMsg::showMessage($p_msg, $p_send_header, $p_show_big, 'note');
  } // Ende Methode showTask



  /**
  * Zeigt Hinweise/Beschreibungen an.
  *
  * Beispiele: "dies ist dazu da / beachten sie folgendes details / machen sie sich keine sorgen wenn...", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Warnung
  */
  function showAction($p_msg, $p_send_header = false, $p_show_big = false)
  {
    return SysMsg::showMessage($p_msg, $p_send_header, $p_show_big, 'action');
  } // Ende Methode showTask



  /**
  * Zeigt (Datei-)Kollisionen an.
  *
  * Beispiele: "dies ist dazu da / beachten sie folgendes details / machen sie sich keine sorgen wenn...", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Warnung
  */
  function showHit($p_msg, $p_send_header = false, $p_show_big = false)
  {
    return SysMsg::showMessage($p_msg, $p_send_header, $p_show_big, 'hit');
  } // Ende Methode showTask



  /**
  *
  */
  function showMessage($p_msg, $p_send_header, $p_show_big, $p_msg_type)
  {
		//$icon_url  = null;
		$class     = '';
		$style     = '';
		$big_class = '';

		switch ($p_msg_type)
		{
			case 'info' :
			case 'warning' :
			case 'error' :
			case 'task' :
				//$icon_url  = 'i_msg_'.$p_msg_type.'.gif';
				$class     = ' user_'.$p_msg_type;
				$big_class = ' big_'.$p_msg_type;
				break;

			case 'note' :
				$style = 'style="color:gray;"';
				$p_msg = 'Note : '.$p_msg;
				break;

			case 'action' :
				$class = ' action';
				break;

			case 'hit' :
				$class = ' hit';
				break;

			case 'msg' :
			default :
				break;
		}

	/*	if (empty($icon_url) == false)
		{
			$style = 'style="background-image : url(./_img/'.$icon_url.'); padding-left: 20px;"';
    }*/

    if (empty($p_show_big))
    {
   		$big_class = '';
    }

//    $content = '<TABLE class="user_message'.$class.$big_class.'"><tr><td '.$style.'>'.SysMsg::stripHTML($p_msg).'</td></tr></TABLE>'."\n";
    $content = '<div class="user_message'.$class.$big_class.'" '.$style.'>'.SysMsg::stripHTML($p_msg).'</div>'."\n";

    return SysMsg::display($content, $p_send_header);

  } // Ende Methode showMessage



  /**
  * Zeigt von Warnungen für den Benutzer an (rotes Feld mit rotem Ausrufezeichen).
  *
  * Beispiele: "Datei nicht gefunden.", "Löschen fehlgeschlagen.", etc.
  * Formatierende HTML-tags sind erlaubt.
  *
  * @param	string	$msg	benutzerdefinierte Warnung
  */
  function display($msg, $sendHeader = false)
  {
  	if ($sendHeader)
  	{
  		$charset = MC_CHAR_SET;
			echo <<<MSGHEADER
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">
<HTML>
<HEAD>
<TITLE></TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset={$charset}">
<link href="../../admin/css/style.php" rel="stylesheet" type="text/css">
</head>
<BODY>
MSGHEADER;
		}

		if (defined('sysmsg_failed'))
		{
			$replace = array(
				'{FAIL}'      => sysmsg_failed,
				'{FAILED}'    => sysmsg_failed,
				'{CANCEL}'    => sysmsg_canceled,
				'{CANCELED}'  => sysmsg_canceled,
				'{SKIP}'      => sysmsg_skipped,
				'{SKIPPED}'   => sysmsg_skipped,
				'{SUCCEED}'   => sysmsg_succeed,
				'{SUCCEEDED}' => sysmsg_succeed,
				'{DONE}' => sysmsg_done,
			);
			$pattern = array_keys($replace);
			$msg = str_replace($pattern, $replace, $msg);
		}

		echo $msg;

		return;
  } // Ende Methode display



/**
  * entfernt unerlaubte HTML-tags aus nachrichten
  *
  * @param string	$p_string nachricht
  * @author				Steffen Groß
  */
	function stripHTML($p_string)
	{
		if (is_string($p_string) == false)
		{
			return $p_string;
		}

		return strip_tags($p_string, "<hr><b><i><br><br/><p><p/><pre><a></a><h1><h2><h3><h4><h5><h6>");
	}




} // Ende Klasse SysMsg



function dump(&$param)
{

	ob_start();
//	echo $l_debug;

	var_dump($param);
	$content = ob_get_contents();

	ob_end_clean();
	popout($content);

	return;
}



function out($param = "")
{

	ob_start();
//	echo $l_debug;

	if (is_array($param) == true)
	{
		$param = implode("<br>", $param);
	}
	echo $param;

	$content = ob_get_contents();
	ob_end_clean();

	popout($content);

	return;
}



function popout($p_content)
{
	echo '<pre style="padding:0px;margin:3px 0px;font-size:11px;">'.$p_content.'</pre><hr style="padding:0px;margin:0px;" />';
	return;
}



/**
 *
 */
function startTimer($name)
{
	if (empty($name) ) {
		echo '<br>WARNING ' . __METHOD__ . ": parameter name is empty<br>";
	}
	else if ( empty($GLOBALS[$name]) ) {
		$GLOBALS[$name] = microtime(true);
	}
	else {
		echo '<br>WARNING ' . __METHOD__ . ": timer '{$name}' already exists!<br>";
	}
} // end of function startTimer



/**
 *
 */
function getTimer($name, $deleteTimer = false)
{
	if ( empty($GLOBALS[$name]) ) {
		return '<br>WARNING ' . __METHOD__ . ": timer '{$name}' does not exist!<br>";
	}

	$microtimeStart = $GLOBALS[$name];
	$microtimeEnd = microtime(true);
	$microRunTime = $microtimeEnd - $microtimeStart;

	if ($deleteTimer) {
		deleteTimer($name);
	}

	return $microRunTime;
} // end of function getTimer



/**
 *
 */
function displayTimer($name, $deleteTimer = false) {
	$microRunTime = getTimer($name, $deleteTimer);
	echo "<br>timer '{$name}' : {$microRunTime}<br>";
} // end of function displayTimer



/**
 *
 */
function deleteTimer($name)
{
	if ( empty($GLOBALS[$name]) ) {
		return '<br>WARNING ' . __METHOD__ . ": timer {$name} does not exist!<br>";
	}

	unset($GLOBALS[$name]);
} // end of function deleteTimer



/**
 *
 */
function getRunTime()
{
	if ( ! defined('MC_INIT_MICROTIME') ) {
		return '<br>WARNING ' . __METHOD__ . ': MC_INIT_MICROTIME is not defined!<br>';
	}

	$microtimeStart = MC_INIT_MICROTIME;
	$microtimeEnd = microtime(true);
	$microRunTime = microtime(true) - MC_INIT_MICROTIME;

	return $microRunTime;
} // end of function getRunTime



/**
 *
 */
function displayRunTime()
{
	if ( ! mc_Debug::isEnabled($msgPrefix) ) {
		return '';
	}

	$runTime = getRunTime();

	echo <<<RUNTIME
{$msgPrefix}<hr />
<center style="font-family:verdana; font-size:9px;">
	code generated in {$runTime} sec
</center>
RUNTIME;
} // end of function displayRunTime
