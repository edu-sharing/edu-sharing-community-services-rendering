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

/**
 * SysMsg
 *
 * @author [Autor]
 */
class EsCache
{

	/**
	 * constructor
	 */
	public function __construct()
	{
		// ??
	} // end construktor



	/**
	 * Zeigt Nachrichten für den Benutzer an.
	 *
	 * Beispiele: "Wird geprüft/geladen/etc. ...", etc.
	 * Formatierende HTML-tags sind erlaubt.
	 *
	 * @param	string	$msg	benutzerdefinierte Information
	 */
	public static function showMsg($p_msg, $p_send_header = false)
	{
		$content = '<TABLE class="user_message"><tr><td>'.SysMsg::stripHTML($p_msg).'</td></tr></TABLE>';

		SysMsg::showMessage($content, $p_send_header);

		return;
	} // end function showUserInfo



	/**
	 * Zeigt Informationen und Bestätigungen für den Benutzer an (grünes Feld mit grünem Ausrufezeichen).
	 *
	 * Beispiele: "Eintrag angelegt.", "Eintrag gelöscht.", "Suche beendet", etc.
	 * Formatierende HTML-tags sind erlaubt.
	 *
	 * @param	string	$msg	benutzerdefinierte Information
	 */
	public static function showInfo($p_msg, $p_send_header = false)
	{
		$icon_url = '';
		if (defined("MC_IMG_URI") )
		{
			$icon_url = MC_IMG_URI.'i_msg_good.gif';
		}

		$content = '<TABLE class="user_message user_info"><tr><td style="background-image:url('.$icon_url.');">'.SysMsg::stripHTML($p_msg).'</td></tr></TABLE>';

		SysMsg::showMessage($content, $p_send_header);

		return;
	} // end function showUserInfo



	/**
	 * Zeigt von Warnungen für den Benutzer an (rotes Feld mit rotem Ausrufezeichen).
	 *
	 * Beispiele: "Datei nicht gefunden.", "Löschen fehlgeschlagen.", etc.
	 * Formatierende HTML-tags sind erlaubt.
	 *
	 * @param	string	$msg	benutzerdefinierte Warnung
	 */
	public static function showWarning($p_msg, $p_send_header = false)
	{
		$icon_url = '';
		if (defined("MC_IMG_URI"))
		{
			$icon_url = MC_IMG_URI.'i_msg_bad.gif';
		}

		$content = '<TABLE class="user_message user_warning"><tr><td style="background-image:url('.$icon_url.');">'.SysMsg::stripHTML($p_msg).'</td></tr></TABLE>';


		SysMsg::showMessage($content, $p_send_header);

		return;
	} // end function showUserWarning



	/**
	 * Zeigt Fehlermeldungen für den Benutzer an (wie Warnung).
	 *
	 * Beispiele: "Datei nicht gefunden.", "Löschen fehlgeschlagen.", etc.
	 * Formatierende HTML-tags sind erlaubt.
	 *
	 * @param	string	$msg	benutzerdefinierte Warnung
	 */
	public static function showError($p_msg, $p_send_header = false)
	{
		SysMsg::showWarning($p_msg, $p_send_header);
		return;
	} // end function showUserWarning



	/**
	 * Zeigt von Warnungen für den Benutzer an (rotes Feld mit rotem Ausrufezeichen).
	 *
	 * Beispiele: "Datei nicht gefunden.", "Löschen fehlgeschlagen.", etc.
	 * Formatierende HTML-tags sind erlaubt.
	 *
	 * @param	string	$msg	benutzerdefinierte Warnung
	 */
	protected static function showMessage($p_msg, $p_send_header = false)
	{
		if ($p_send_header)
		{
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset='.MC_CHAR_SET.'">
		<link href="'.MC_ROOT_URI.'/design/m2xcross/style/intern_style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		'.$p_msg.'
	</body>
</html>';
		}
		else
		{
			echo $p_msg;
		}

		return;
	} // end function showUserWarning


	/**
	 * entfernt unerlaubte HTML-tags aus nachrichten
	 *
	 * @param string	$p_string nachricht
	 * @author				Steffen Groß
	 */
	protected static function stripHTML($p_string)
	{
		if (is_string($p_string))
		{
			return strip_tags($p_string, "<hr><b><i><br><br/><p><p/><pre>");
		}
		else
		{
			return $p_string;
		}
	} //  end method stripHTML


} // end class SysMsg


?>