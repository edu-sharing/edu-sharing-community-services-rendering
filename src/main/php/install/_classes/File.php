<?php
/*
* $McLicense$
*
* $Id$
*
*/


require_once(MCP_LIB_PATH."Conf.php");

define("CONF_MODULES_FILENAME",	MC_ROOT_PATH.'conf'.DIRECTORY_SEPARATOR.'modules.conf.php');
define("CONF_SYSTEM_FILENAME",	MC_ROOT_PATH.'conf'.DIRECTORY_SEPARATOR.'system.conf.php');
define("CONF_DEFINES_FILENAME",	MC_ROOT_PATH.'conf'.DIRECTORY_SEPARATOR.'defines.conf.php');

class File
	extends Plattform
{
	var $PVersion;

	var $TEST;
	var $LASTCHANGE;

	var $OS;
	var $CMD_QUOTE;

	var $OUCH;
	var $collision_tracker;

	var $IS_FILE_WRITABLE_ERROR;

	var $silent;

	function File($p_overwrite = false)
	{
		$this->Plattform();

		$this->PVersion = &$GLOBALS['l_PVersion'];
		$this->Conf     = &$GLOBALS['l_Conf'];

		// checking operating system
		$this->OS = php_uname();
		$this->CMD_QUOTE = (strpos($this->OS, "Windows") === false) ? '' : '"';

		$this->TEST = IS_TEST;
		$this->PFINFO = '';
		$this->LASTCHANGE = $this->PVersion->getLastChange($this->PFINFO, $this->Conf);

		$this->OUCH = !empty($p_overwrite);

		$this->resetCt();

		$this->IS_FILE_WRITABLE_ERROR = FALSE;

		$this->silent = FALSE;

		if ($this->checkBackupPath() == false)
		{
			return false;
		}

		return true;

	} // end Constructor


	// collision tracker functions (tracks possible collisions of user customized files with files from update)
	function resetCt()   { $this->collision_tracker = array(); }

	function getCtSize() { sizeof($this->collision_tracker); }

	function addCtEntry($p_file) { $this->collision_tracker[] = $p_file; }

	function getCtAsString($p_glue = '') { implode($p_glue, $this->collision_tracker); }



	function update($from_path, $to_path, $backup_path, $check_for_permissions_only = false)
	{
		if (file_exists($to_path) == false) {
			if ($check_for_permissions_only) {
				if ( file_exists(dirname($to_path)) && !is_writable(dirname($to_path)) ) {
					SysMsg::showWarning("permission error: directory '".dirname($to_path)."' is not writeable!");
					$this->IS_FILE_WRITABLE_ERROR = true;
				}
			}
			else {
				if ($this->TEST == true) {
					SysMsg::showInfo("mkdir(".$to_path.", 0770);");
				}
				else {
					mkdir($to_path, 0770);
				}
			}
		}

		if (file_exists($backup_path) == false) {
			if (!$check_for_permissions_only && $this->TEST == false) {
				mkdir($backup_path, 0770);
			}
		}

		if (is_dir($from_path)) {

			if (!$handle = opendir($from_path)) {
				Plattform::error($from_path, 'unable to open path');
				return false;
			}

			while ( ($file = readdir($handle)) !== false) {
				$exists = false;
				$collision = false;

				if (($file == ".") || ($file == ".."))	{
					//durchlauf abbrechen, wenn self-dir oder parent-dir
					continue;
				}

				if (is_dir($from_path.$file))  {
					$this->update($from_path.$file.DIRECTORY_SEPARATOR, $to_path.$file.DIRECTORY_SEPARATOR, $backup_path.$file.DIRECTORY_SEPARATOR, $check_for_permissions_only);
				}

				if ( is_file($from_path.$file) ) {

					// auf kollision mit von benutzer geaenderten files pruefen und ggf. vermerken
					if (file_exists($to_path.$file) == true) {
					    $exists = true;
					    if (!$check_for_permissions_only) {
								if (filemtime($to_path.$file) > $this->LASTCHANGE) {
									$this->addCtEntry($to_path.$file);
									$collision = true;
								}
					    }
					    else {
					    	if ( !is_writable($to_path.$file) ) {
						    SysMsg::showWarning("permission error: file '".$to_path.$file."' is not writeable for PHP!");
						    $this->IS_FILE_WRITABLE_ERROR = true;
							}
					  }
					}

					if ($this->OUCH == true) {
						// ge�nderte dateien d�rfen �berschrieben werden
						if ( !$check_for_permissions_only) {
							if ($exists == true) {
								SysMsg::showMsg("backup file: ".$to_path.$file);
								if ($this->TEST == false) {
									copy($to_path.$file, $backup_path.$file);
								}
								#echo "<span class='replace'>replace file ".$to_path.$file."</span><br>";
								SysMsg::showHit("replace file ".$to_path.$file);
							}
							else {
								SysMsg::showMsg("add file ".$to_path.$file);
							}
							if ($this->TEST == false) {
								copy($from_path.$file, $to_path.$file);
							}
						}
					}
					else {
						if (!$check_for_permissions_only)
						{
							if ($collision == true) {
								SysMsg::showWarning("not touched (file has been modified): ".$to_path.$file);
							}
							else
							{
								SysMsg::showMsg("backup file: ".$to_path.$file);
								if ($exists == true)	{
									#echo "<span class='replace'>replace file ".$to_path.$file."</span><br>";
									SysMsg::showWarning("replace file ".$to_path.$file);
								}
								else {
									SysMsg::showMsg("add file ".$to_path.$file);
								}
								if ($this->TEST == false) {
									copy($from_path.$file, $to_path.$file);
								}
							}
						}
					}
				}
			}
			closedir($handle);
		}
		else {
			Plattform::error($from_path, 'not a directory');
			unset($changes);
			return false;
		}

		unset($changes);
		return true;

	} // end function update





	function replaceCommonFileToken($path)
	{

		global $TOKEN_REPLACE_LIST;

		if (is_array($TOKEN_REPLACE_LIST) == false) {
			Plattform::error($TOKEN_REPLACE_LIST, 'parameter $TOKEN_REPLACE_LIST is not an array');
		}

		if (is_dir($path)) {
		  if (!$handle = opendir($path)) {
		  	return 0;
		  }

			while ( ($file = readdir($handle)) !== false) {

				if ( ($file == ".") || ($file == "..") ) {
					// skip current directory or parent directory
					continue;
				}

				$mcf = $path.$file;

			 	// scan subdirectory
				if ( is_dir($mcf) ) {
					$this->replaceCommonFileToken($path.$file.DIRECTORY_SEPARATOR);
				}

				if ( is_file($mcf) ) {

					$content = implode("", ($mcfc=file($mcf)));
					$hash = md5($content);

					$content = strtr($content, $TOKEN_REPLACE_LIST);

					if ( $hash != md5($content) ) { // no changes
						fwrite($mcfh = fopen($mcf, "w+"), $content);
						fclose($mcfh);
					}
					if ( preg_match('@\[\[\[[A-Z_]]]]@', $content, $match) ) {
						SysMsg::showWarning('unreplaced token "'.$match[0].'" found in file '.$mcf);
					}
				}
			}
			closedir($handle);
		}
		else {
			return 0;
		}
		clearstatcache();
		return 1;

	} // end function replaceCommonFileToken



	function replaceFileToken($p_file_name, $p_replace, $p_source_file_name = null)
	{
		if (empty($p_source_file_name))
		{
			$p_source_file_name = $p_file_name;
		}

		$p_file_name_src  = MC_ROOT_PATH.$p_source_file_name;
		$p_file_name_dest = MC_ROOT_PATH.$p_file_name;

		if ( !is_array($p_replace) ) {
			SysMsg::showError("not an array : '".$p_replace."' ! (file ".__FILE__.", line ".__LINE__.")");
			return false;
		}

		if ( !is_file($p_file_name_src) ) {
			SysMsg::showError("not a file : '".$p_file_name_src."' ! (file ".__FILE__.", line ".__LINE__.")");
			return false;
		}

		if ( !is_writable(dirname($p_file_name_dest)) ) {
			SysMsg::showError("not writable : '".dirname($p_file_name_dest)."' ! (file ".__FILE__.", line ".__LINE__.")");
			return false;
		}

		$l_file_content   = implode("", file($p_file_name_src));
		$l_file_hash = md5($l_file_content);

		$l_file_content = str_replace(array_keys($p_replace), $p_replace, $l_file_content);

		if ( $l_file_hash != md5($l_file_content) ) {
			// changes happened
			if ( !fwrite($l_handle = fopen($p_file_name_dest, "w+"), $l_file_content) ) {
				SysMsg::showWarning("file not writeable : '".$p_file_name_dest."' ! (file ".__FILE__.", line ".__LINE__.")");
				return false;
			}
			fclose($l_handle);

			clearstatcache();

			SysMsg::showMsg('token replaced in file '.basename($p_file_name));

			return 2;
		}

		clearstatcache();

		SysMsg::showMsg('no token replaced in file '.basename($p_file_name).' (no token found)');

		return 1;

	} // end function replaceFileToken




	function getAffectedCustomFileChanges()
	{
		if ($this->getCtSize() == 0)
		{
			if ($this->TEST) {
				return patch_test_label_02."<p>";
			}

			return patch_label_02."<p>";
		}

		if ($this->OUCH)
		{
			$comment = patch_test_label_09a;
		}
		else
		{
			$comment = patch_test_label_09b;
		}

		if ($this->TEST)
		{
			return "<span class='ouch_info'>".patch_test_label_09." ".$comment."<br /> ".patch_label_13."<a class=\"link\" href='".Conf::getFCLogUri()."' target='_blank'>'".Conf::getFCLogUri()."'</a>.</span><p />";
		}

		$uch_logfile = fopen(Conf::getFCLogPath(), "w+");
		$l_ct_string = $this->getCtAsString("<br>\n");

		if ($uch_logfile)
		{
			fwrite($uch_logfile, $l_ct_string);
			fclose($uch_logfile);

			return "<span class='ouch_info'>".patch_label_09." ".$comment."<br /> ".patch_label_13."<a class=\"link\" href='".Conf::getFCLogUri()."' target='_blank'>'".Conf::getFCLogUri()."'</a>.</span><p />";
		}

		if ($this->TEST == false)
		{
			return "<span class='ouch_error'>".patch_label_10."</span><div>".$l_ct_string."</div><p />";
		}

		return '';
	} // end function getAffectedCustomFileChanges






	function readFileContent($f_name, $as_array = false) {
		if (!$contents = file($f_name)) {
			SysMsg::showError("error: cannot read config file '$f_name'<br>");
			return false;
		}

		if ($as_array == false)
		{
			return implode('', $contents);
		}

		return $contents;
	} // end function readFileContent





	function writeFileContent($f_name, $p_new_content)
	{
		if (is_writable($f_name) == false)
		{
			SysMsg::showError("error: cannot write file '$f_name', file is not writable!<br />");
			return false;
		}

		if ($this->createBackupFromFile($f_name, true))
		{
			if ($handle = fopen($f_name, 'w'))
			{
				fwrite($handle, $p_new_content);
				fclose($handle);
			}
			else
			{
				SysMsg::showError("error: cannot open file '$f_name' (fopen failed)<br />");
				return false;
			}
		}
		else
		{
			SysMsg::showError("error: cannot create backup from file '$f_name'<br />");
			return false;
		}

		return true;
	} // end function writeFileContent



	function createBackupFromFile($p_dest, $p_rename = false)
	{
		$p_rename = (bool)$p_rename;

		if ($p_rename == true)
		{
			if (rename($p_dest, $p_dest.'.'.time().'.BAK') == false)
			{
				return false;
			}
		}
		else
		{
			if (copy($p_dest, $p_dest.'.'.time().'.BAK') == false)
			{
				return false;
			}
		}

		return true;
	} // end function createBackupFromFile




	function appendParamToConfSection($p_old_content, $p_section_name, $p_check_for_string, $p_add_content) {

		$p_new_content = $p_old_content;

		$match = array();
		// ermitteln des vollst�ndigen bereiches zwischen zwei sektions-namen
		// (erfasst kommentar der sektion sowie alle nachfolgenden parameter, kommentare und leerzeilen)
		// ACHTUNG : kann NICHT auf die letzte sektion zugreifen!
		preg_match("/# ".$p_section_name."(.*)?[\s]*# [A-Z_\x20\t]/Us", $p_old_content, $match);

		if ( isSet($match[1]) && strlen($match[1]) > 10 && strpos($match[1], $p_check_for_string) === false)
		{
			return str_replace($match[1], $match[1].$p_add_content, $p_old_content);
		}

		return false;

	} // end function appendParamToConfSection




	function fetchConfSection($p_content, $p_section)
	{

		$match = array();
		// ermitteln des vollst�ndigen bereiches zwischen zwei sektions-namen
		// (erfasst kommentar der sektion sowie alle nachfolgenden parameter, kommentare und leerzeilen)
		// ACHTUNG : kann NICHT auf die letzte sektion zugreifen!
		if (is_array($p_content))
		{
			$l_content = implode('', $p_content);
		}
		else
		{
			$l_content = $p_content;
		}

		preg_match("/# ".$p_section."(.*)?[\s]*# [A-Z_\x20\t]/s", $l_content, $match);
		if ( isSet($match[1]) && strlen($match[1]) > 10 )
		{
			if (is_array($p_content))
			{
				$l_match_start = strpos($l_content, $match[1]);
				$l_match_end   = $l_match_start + strlen($match[1]);
				$l_len_count = 0;
				$l_content = array();
				foreach ($p_content as $l_line)
				{
					$l_len_count += strlen($l_line);

					if ($l_len_count >= $l_match_start)
					{
						$l_content[] = $l_line;
					}
					if ($l_len_count >= $l_match_end)
					{
						$l_content[] = $l_line;
						break;
					}
				}
				return $l_content;
			}
			else
			{
				return $match[1];
			}
		}

		return false;

	} // end function appendParamToConfSection





	function moveLine($p_content, $p_catch_string, $p_target_string, $p_add_below = true)
	{
		if (is_array($p_content) == false)
		{
			Plattform::error(null, 'error: content is not an array');
			return false;
		}

		$l_new_content = array();
		$l_catch = null;

		// catch and strip row from content
		foreach ($p_content as $row)
		{
			if (strpos(trim($row), $p_catch_string) === 0)
			{
				$l_catch = $row;
			}
			else
			{
				$l_new_content[] = $row;
			}
		}

		if ($l_catch === null)
		{
			if ($this->silent == false)
			{
				SysMsg::showWarning('can\'t find a matching string for "'.htmlentities($p_catch_string).'"!');
			}
			return false;
		}

		return $this->addLine($l_new_content, $p_target_string, $l_catch, $p_add_below);

	} // end function moveParamBelowTarget




	function addLine($p_content, $p_catch_string, $p_add_string, $p_add_below = true)
	{
		if (is_array($p_content) == false)
		{
			Plattform::error(null, 'error: content is not an array');
			return false;
		}

		$l_new_content = array();
		$l_added = false;

		// insert catched row at target position
		foreach ($p_content as $row)
		{
			if ($p_add_below == true)
			{
				$l_new_content[] = $row;
			}
			if (strpos(trim($row), $p_catch_string) === 0)
			{
				$l_new_content[] = $p_add_string;
				$l_added = true;
			}
			if ($p_add_below == false)
			{
				$l_new_content[] = $row;
			}
		}

		if ($p_add_below == false)
		{
			if ($this->silent == false)
			{
				SysMsg::showWarning('can\'t find a matching string for "'.htmlentities($p_catch_string).'"!');
			}
			return false;
		}

		return $l_new_content;

	} // end function moveParamBelowTarget





	function removeLine($p_content, $p_catch_string)
	{
		if (is_array($p_content) == false)
		{
			Plattform::error(null, 'error: content is not an array');
			return false;
		}

		$l_new_content = array();
		$l_removed = false;

		// catch and strip row from content
		foreach ($p_content as $row)
		{
			if (strpos(trim($row), $p_catch_string) === 0)
			{
				// leave out
				$l_removed = true;
			}
			else
			{
				$l_new_content[] = $row;
			}
		}

		if ($l_removed == false)
		{
			if ($this->silent == false)
			{
				SysMsg::showWarning('can\'t find a matching string for "'.htmlentities($p_catch_string).'"!');
			}
			return false;
		}


		return $l_new_content;

	} // end function removeLine





	function replaceLine($p_content, $p_catch_string, $p_replace_string)
	{
		if (is_array($p_content) == false)
		{
			Plattform::error(null, 'error: content is not an array');
			return false;
		}

		$l_new_content = array();
		$l_replaced = false;

		// catch and strip row from content
		foreach ($p_content as $row)
		{
			if (strpos(trim($row), $p_catch_string) === 0)
			{
				$l_replaced = true;
				$l_new_content[] = $p_replace_string;
			}
			else
			{
				$l_new_content[] = $row;
			}
		}

		if ($l_replaced == false)
		{
			if ($this->silent == false)
			{
				SysMsg::showWarning('can\'t find a matching string for "'.htmlentities($p_catch_string).'"!');
			}
			return $p_content;
		}


		return $l_new_content;

	} // end function replaceLine





	function searchLine($p_content, $p_catch_string, $p_offset = 0)
	{
		if (is_array($p_content) == false)
		{
			Plattform::error(null, 'error: content is not an array');
			return false;
		}

		$p_offset = intval($p_offset);

		// search catch string
		foreach ($p_content as $id => $row)
		{
			if ($p_offset <= $id)
			{
//				if (strpos(trim($row), $p_catch_string) === 0)
				if (strpos(trim($row), $p_catch_string) !== false)
				{
					return $id;
				}
			}
		}

		return false;

	} // end function searchLine





	function getLineByRegExp($p_pattern, $p_content, $p_offset = 0)
	{
		if (is_array($p_content) == false)
		{
			Plattform::error(null, 'error: content is not an array');
			return false;
		}

		$p_offset = intval($p_offset);

		// search catch string
		foreach ($p_content as $id => $row)
		{
			if ($p_offset <= $id)
			{
				if (preg_match($p_pattern, $row))
				{
					return $id;
				}
			}
		}

		return false;

	} // end function getLineByRegExp






	function addContentToFile($p_filename, $p_append_content, $p_check_for_string = null)
	{

		SysMsg::showInfo("add content to ".$p_filename);

		$l_old_content = $this->readFileContent($p_filename);
		if ($l_old_content === false)
		{
			return false;
		}

		if ( !empty($p_check_for_string) && strpos($l_old_content, $p_check_for_string) !== false)
		{
			SysMsg::showInfo("skipped. (checked for ".$p_check_for_string.": already exists)");
			return true;
		}

		$tmp = rtrim($l_old_content);
		if (substr($tmp, -2) == '?'.'>')
		{
			$l_new_content  = substr($tmp, 0, -2)."\n";
			$l_new_content .= $p_append_content."\n?".'>';
		}
		else
		{
			$l_new_content .= $p_append_content;
		}

		return $this->writeFileContent($p_filename, $l_new_content);

	} // end function addContentToFile






	function delete($p_filelist, $check_for_permissions_only = false)
	{

		if ( is_array($p_filelist) == false )
		{
			SysMsg::showError("parameter \$p_filelist is not an array !");
			return false;
		}
		else if (sizeof($p_filelist) == 0)
		{
			if ($check_for_permissions_only == false)
			{
				SysMsg::showInfo("no files/directories to delete (empty list).");
			}
			return true;
		}

		$p_src_path    = MC_ROOT_PATH;
		$p_backup_path = Conf::getBackupPath();

		if (is_string($p_filelist) )
		{
			$p_filelist = array($p_filelist);
		}

		foreach ( $p_filelist as $l_filepath )
		{
			$l_src_filename  = $p_src_path.$l_filepath;
			$l_dest_filename = $p_backup_path.$l_filepath;

			if ($l_filepath[0] == "/" || $l_filepath[1] == ':')
			{
				SysMsg::showError("absolute path statement found ('".$l_src_filename."')! file {SKIPPED} (absolute paths are forbidden).");
			}

			if (strpos($l_filepath, '../') !== false || strpos($l_filepath, '..\\') !== false)
			{
				SysMsg::showError("upward directed path statement found ('".$l_src_filename."')! file {SKIPPED} (upward directed paths are forbidden).");
			}


			if (!file_exists($l_src_filename))
			{
		    if ($check_for_permissions_only == false)
		    {
					SysMsg::showWarning("cannot find file '".$l_src_filename."' for removing (already removed?). file {SKIPPED}. (no problem!)");
		    }
		    continue;
			}

			$this->createDirectory(dirname($l_dest_filename));

			if (filemtime($l_src_filename) > $this->LASTCHANGE)
			{
				$this->addCtEntry($l_src_filename." (".filemtime($l_src_filename)." > ".$this->LASTCHANGE.")");
			}

			if ($this->TEST == true || $check_for_permissions_only == true)
			{
				if ($check_for_permissions_only == false)
				{
					SysMsg::showMsg("would rename('".$l_src_filename."', '".$l_dest_filename."');");
				}
			}
			else
			{
				if (rename($l_src_filename, $l_dest_filename))
				{
					SysMsg::showInfo("file".DIRECTORY_SEPARATOR."directory '".$l_src_filename."' deleted (moved to backup)");
				}
			}

		}

		return true;

	} // end function delete






	function move($p_path_list)
	{
		$p_src_path = MC_ROOT_PATH;

		if ( !is_array($p_path_list) )
		{
			SysMsg::showError("parameter \$p_path_list is not an array !");
			return false;
		}
		else if (sizeof($p_path_list) == 0)
		{
			SysMsg::showInfo("no files/directories to move (empty list).");
			return false;
		}

		while ( list($from_path, $to_path) = each($p_path_list) )
		{
			if ( file_exists($p_src_path.$from_path) )
			{
				if (is_dir($p_src_path.$to_path) == false)
				{
					$this->createDirectory(dirname($p_src_path.$to_path));
				}
				if ($this->TEST == true)
				{
//					$this->showInfo("rename('".$p_src_path.$l_src_filename."', '".$p_src_path.$l_dest_filename."');");
					SysMsg::showInfo("rename('".$p_src_path.$from_path."', '".$p_src_path.$to_path."');");
				}
				else
				{
					if (rename($p_src_path.$from_path, $p_src_path.$to_path))
					{
						SysMsg::showInfo('path "'.$p_src_path.$from_path.'" moved to "'.$p_src_path.$to_path.'"');
					}
				}
			}
			else
			{
				SysMsg::showWarning("can't move path '".$p_src_path.$from_path."' (path doesn't exist). path {SKIPPED}.");
			}
		}

		return true;

	} // end function move





/*
	function copyPath($from_path, $to_path, $check_for_permissions_only = false)
	{
		$p_src_path = MC_ROOT_PATH;

		if ( file_exists($p_src_path.$from_path) )
		{
			if ($check_for_permissions_only == true)
			{
				if ( !file_exists($p_src_path.$to_path) )
				{
					if ( file_exists(dirname($p_src_path.$to_path)) && !is_writable(dirname($p_src_path.$to_path)) )
					{
						SysMsg::showError("permission error: directory '".dirname($to_path)."' is not writeable for PHP!");
						$this->IS_FILE_WRITABLE_ERROR = true;
					}
				}
			}
			else
			{
				if ($this->createDirectory(dirname($p_src_path.$to_path)) == true)
				{
					if ($this->TEST == true)
					{
						SysMsg::showInfo("copy('".$p_src_path.$from_path."', '".$p_src_path.$to_path."');");
					}
					else
					{
						if (is_file($p_src_path.$from_path))
						{
							copy($p_src_path.$from_path, $p_src_path.$to_path);
						}
						else if (is_dir($p_src_path.$from_path))
						{
							$this->rcopy($p_src_path.$from_path, $p_src_path.$to_path);
						}
						else
						{
							SysMsg::showError("unknown source type from '".$p_src_path.$to_path."' ( is_dir == false && is_file == false ).");
						}
					}
				}
				else
				{
					$this->showError("can't copy to path '".$p_src_path.$to_path."' (can't access target's parent directory).");
				}
			}
		}
		else
		{
			SysMsg::showError("can't copy from path '".$p_src_path.$from_path."' (source doesn't exist).");
		}

		return true;

	} // end function copyPath
*/



	function copy($p_src_path, $p_dest_path)
	{
		if ( file_exists($p_dest_path) )
		{
			return true;
		}

		if ( file_exists($p_src_path) )
		{
			$l_dirname = dirname($p_dest_path);
			if (file_exists($l_dirname))
			{
				// nyschd
			}
			else
			{
				if ($this->createDirectory($l_dirname) == true)
				{
					// nyschd
				}
				else
				{
					SysMsg::showError("can't copy to path '".$p_dest_path."' (can't access/create target's parent directory).");
					return false;
				}
			}
		}
		else
		{
			SysMsg::showError("can't copy from path '".$p_src_path."' (source doesn't exist).");
			return false;
		}

		if (is_file($p_src_path))
		{
			copy($p_src_path, $p_dest_path);
		}
		else if (is_dir($p_src_path))
		{
			mkdir($p_dest_path);
			chmod($p_dest_path, 0777);
		}
		else
		{
			SysMsg::showError("unknown source type from '".$p_dest_path."' ( is_dir == false && is_file == false ).");
		}

		return true;

	} // end function copy




	function rcopy($from_path, $to_path, $p_show_msg = false)
	{
		if ($this->TEST == true)
		{
			SysMsg::showWarning('patch is running in test mode - directory copy skipped!');
			return false;
		}

		if ($this->createDirectory($to_path) == false)
		{
			return false;
		}

    if ($handle = opendir($from_path))
    {
			$l_state = true;
      while ( ($file = readdir($handle)) !== false)
      {
        if ( ($file == ".") || ($file == "..") )
        {
        	continue;
        }

        if (is_dir($from_path.DIRECTORY_SEPARATOR.$file) )
        {
        	$l_state = $this->rcopy($from_path.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR, $to_path.$file.DIRECTORY_SEPARATOR);
					continue;
        }

        if (is_file($from_path.DIRECTORY_SEPARATOR.$file) )
        {
        	$l_state = copy($from_path.DIRECTORY_SEPARATOR.$file, $to_path.$file);
        	if ($p_show_msg)
        	{
        		if ($l_state)
        		{
        			SysMsg::showInfo('copy "'.$from_path.DIRECTORY_SEPARATOR.$file.'" to "'.$to_path.$file.'" {SUCCEED}.');
        		}
        		else
        		{
        			SysMsg::showError('copy "'.$from_path.DIRECTORY_SEPARATOR.$file.'" to "'.$to_path.$file.'" {FAILED}.');
        		}
        	}

        	if (is_file($to_path.$file) )
        	{
						@chmod($to_path.$file, 0777);
					}
        }

      } // end while

      closedir($handle);
		}
		else
		{
			SysMsg::showError('can not open path "'.$from_path.'"');
    	$l_state = false;
    }

    return $l_state;
	} // end function rcopy



	function rdelete($p_target_path)
	{
    if(!$dir_handle = @opendir($p_target_path))
    {
    	return;
    }

    while (($obj = readdir($dir_handle)) !== false)
    {
      if ($obj == '.' || $obj == '..')
      {
      	continue;
      }

      if (is_dir($dir.DIRECTORY_SEPARATOR.$obj))
      {
      	deleteDirectory($p_target_path.DIRECTORY_SEPARATOR.$obj);
			}
      else
      {
      	@unlink($dir.DIRECTORY_SEPARATOR.$obj);
			}
    }

    closedir($dir_handle);
    @rmdir($p_target_path);

		return true;
	} //  end function deleteDirectory




	function createDirectory($p_target_path)
	{

		if ( is_dir($p_target_path) )
		{
			if (is_writable($p_target_path) == false)
			{
				// path already exists but is NOT writeable
				SysMsg::showWarning("skip creating directory '$p_target_path' (already exists). Warning : existing directory is NOT WRITEABLE!");
				$this->IS_FILE_WRITABLE_ERROR = true;
				return false;
			}
		}
		else
		{
			if ($this->createDirectory(dirname($p_target_path)))
			{
				if ($this->TEST == false)
				{
					if (mkdir($p_target_path, 0777) == false)
					{
						SysMsg::showError("can't create directory '$p_target_path' !");
						$this->IS_FILE_WRITABLE_ERROR = true;
						return false;
					}
				}
			}
			else
			{
				$this->IS_FILE_WRITABLE_ERROR = true;
				return false;
			}
		}

		return true;
	} // end function createDirectory




	function createFile($p_target_path, $p_content)
	{
		$l_dirname = basename($p_target_path);

		if ( is_dir($l_dirname) )
		{
			if ( is_writable($l_dirname) == false )
			{
				// already exists but NOT writeable
				SysMsg::showError("can not create file '$p_target_path'. path '$l_dirname' is NOT WRITEABLE!");
				return false;
			}
		}
		else
		{
			if ($this->createDirectory($l_dirname) == false)
			{
				SysMsg::showError("can not create directory '$l_dirname' !");
				return false;
			}
		}

		if (file_exists($p_target_path))
		{
			SysMsg::showError("file '$p_target_path' already exists!");
			return false;
		}

		$file_handle = @fopen($p_target_path, 'w');
		if (!$file_handle)
		{
			SysMsg::showError("opening (new) file '$p_target_path' failed!");
			return false;
		}

		fwrite($file_handle, $p_content);
		fclose($file_handle);

		return true;
	} // end function createDirectory




	function checkBackupPath()
	{
		$l_parent_dir = dirname(Conf::getBackupPath());

		if ($this->TEST == true)
		{
			if ( is_dir($l_parent_dir) )
			{
				if ( is_writable($l_parent_dir) )
				{
					if (basename($_SERVER['SCRIPT_NAME']) != 'form.php')
					{
						SysMsg::showWarning("parent directory of backup directory is writeable but backup path has not been created yet (patch is running in test mode).");
						return true;
					}
				}
				else
				{
					SysMsg::showWarning("parent directory '$l_parent_dir' of backup directory is NOT WRITEABLE! cannot create backup path!");
					$this->IS_FILE_WRITABLE_ERROR = true;
					return false;
				}
			}
		}
		else
		{
			if ( is_dir(Conf::getBackupPath()) )
			{
				if (is_writable(Conf::getBackupPath()) == false)
				{
					$this->showWarning("backup directory '".Conf::getBackupPath()."' is NOT WRITEABLE!");
					$this->IS_FILE_WRITABLE_ERROR = true;
					return false;
				}
			}
			else
			{
				if ( is_writable($l_parent_dir) )
				{
					if (mkdir(Conf::getBackupPath(), 0777))
					{
						SysMsg::showInfo("backup directory '".Conf::getBackupPath()."' created.");
					}
					else
					{
						SysMsg::showWarning("Error : creating backup directory '".Conf::getBackupPath()."' FAILED!");
						$this->IS_FILE_WRITABLE_ERROR = true;
						return false;
					}
				}
				else
				{
					SysMsg::showWarning("parent directory '$l_parent_dir' of backup directory is NOT WRITEABLE! cannot create backup path!");
					$this->IS_FILE_WRITABLE_ERROR = true;
					return false;
				}
			}
		}

		return true;
	} // end function checkBackupPath


/*

	function replaceFilesByRawHash($check_for_permissions_only = false)
	{
		if (is_dir(MC_ROOT_PATH.'patch/files_hash_raw') == true)
		{
//			return $this->replaceFilesByHash(MC_ROOT_PATH.'patch'.DIRECTORY_SEPARATOR.'files_hash_raw'.DIRECTORY_SEPARATOR, MCP_FILE_TARGET, Conf::getBackupPath(), true, $check_for_permissions_only);
			return $this->replaceFilesByHash(MC_ROOT_PATH.'patch'.DIRECTORY_SEPARATOR.'files_hash_raw'.DIRECTORY_SEPARATOR, MC_ROOT_PATH, Conf::getBackupPath(), true, $check_for_permissions_only);
		}
		else
		{
			if ($check_for_permissions_only == false)
			{
				SysMsg::showInfo("file replacement by raw hash check skipped (no files to replace)");
			}
			return false;
		}
	} // end function replaceFilesByRawHash




	function replaceFilesByStrippedHash($check_for_permissions_only = false)
	{
		if (is_dir(MC_ROOT_PATH.'patch/files_hash_stripped') == true)
		{
//			return $this->replaceFilesByHash(MC_ROOT_PATH.'patch'.DIRECTORY_SEPARATOR.'files_hash_stripped'.DIRECTORY_SEPARATOR, MCP_FILE_TARGET, Conf::getBackupPath(), false, $check_for_permissions_only);
			return $this->replaceFilesByHash(MC_ROOT_PATH.'patch'.DIRECTORY_SEPARATOR.'files_hash_stripped'.DIRECTORY_SEPARATOR, MC_ROOT_PATH, Conf::getBackupPath(), false, $check_for_permissions_only);
		}
		else
		{
			if ($check_for_permissions_only == false)
			{
				SysMsg::showInfo("file replacement by stripped hash check skipped (no files to replace)");
			}
			return false;
		}
	} // end function replaceFilesByStrippedHash



	function replaceFilesByHash($from_path, $to_path, $backup_path, $p_raw = true, $check_for_permissions_only = false)
	{

		if (file_exists($to_path) == false)
		{
			if ($check_for_permissions_only)
			{
				if ( file_exists(dirname($to_path)) && !is_writable(dirname($to_path)) )
				{
					SysMsg::showWarning("permission error: directory '".dirname($to_path)."' is not writeable for PHP!");
					$this->IS_FILE_WRITABLE_ERROR = true;
				}
			}
			else
			{
				if ($this->TEST == true)
				{
					SysMsg::showInfo("mkdir(".$to_path.", 0770);");
				}
				else
				{
					mkdir($to_path, 0770);
				}
			}
		}

		if (file_exists($backup_path) == false)
		{
			if ($check_for_permissions_only == false && $this->TEST == false)
			{
				mkdir($backup_path, 0770);
			}
		}

		if (is_dir($from_path))
		{

			if (!$handle = opendir($from_path))
			{
				SysMsg::showWarning("can't open directory '".dirname($to_path)."'");
				return false;
			}

			while ( ($file = readdir($handle)) !== false)
			{

				$exists = false;

				if (($file == ".") || ($file == ".."))
				{
					//durchlauf abbrechen, wenn self-dir oder parent-dir
					continue;
				}

				if (is_dir($from_path.$file))
				{
//					$this->update($from_path.$file.DIRECTORY_SEPARATOR, $to_path.$file.DIRECTORY_SEPARATOR, $backup_path.$file.DIRECTORY_SEPARATOR, $check_for_permissions_only);
					$this->replaceFilesByHash($from_path.$file.DIRECTORY_SEPARATOR, $to_path.$file.DIRECTORY_SEPARATOR, $backup_path.$file.DIRECTORY_SEPARATOR, $p_raw, $check_for_permissions_only);
				}

				if ( is_file($from_path.$file) )
				{

					$ofile  = $file;
					$l_arr  = explode('.', $ofile);
					$l_hash = array_pop($l_arr);
					$file = implode('.', $l_arr);

					if ( strlen($l_hash) != 32 || empty($file) )
					{
						SysMsg::showWarning("filename error: file '$file' has no hash-extension !");
						continue;
					}

					$is_allowed = false;
					if (file_exists($to_path.$file) == true)
					{

				    $exists = true;

				    if ($check_for_permissions_only == false)
				    {
							$l_content = implode('', file($to_path.$file));
							if ($p_raw == false)
							{
								$l_content = $this->cleanFileContent($l_content);
							}

							$l_root_self = substr(MC_ROOT_PATH, strlen($_SERVER['DOCUMENT_ROOT']));

							$l_content = str_replace(MC_ROOT_URI,   '[[[MC_HASH_URI]]]',  $l_content);
							$l_content = str_replace($l_root_self,  '[[[MC_HASH_SELF]]]', $l_content);
							$l_content = str_replace($_SERVER['DOCUMENT_ROOT'], '[[[MC_HASH_DOCROOT]]]', $l_content);

//							SysMsg::showInfo(md5($l_content)." == ".$l_hash." ?");

							if (md5($l_content) == $l_hash)
							{
								$is_allowed = true;
							}

				    }
				    else
				    {

				    	if ( is_writable($to_path.$file) == false )
				    	{
						    SysMsg::showWarning("permission error: file '".$to_path.$file."' is not writeable for PHP!");
						    $this->IS_FILE_WRITABLE_ERROR = true;
							}

					  }
					}
					else
					{
						$is_allowed = true;
					}

					if ($check_for_permissions_only == false)
					{

						if ($exists == true)
						{

							if ($this->TEST == false)
							{

								if ($is_allowed == true)
								{
									if (copy($to_path.$file, $backup_path.$file))
									{
										SysMsg::showInfo("backup file: ".$to_path.$file);
									}
									else
									{
										SysMsg::showWarning("backup from file ".$to_path.$file." failed. file skipped.");
										continue;
									}

									if (copy($from_path.$ofile, $to_path.$file))
									{
										SysMsg::showInfo("<span class='replace'>replace file ".$to_path.$file." (hash matched : target was unchanged)</span>");
									}
									else
									{
										SysMsg::showWarning("replacing file ".$to_path.$file." failed");
									}
								}
								else
								{
									SysMsg::showInfo("file ".$to_path.$file." not replaced (hash ".md5($l_content)." does not match : target has been changed !)");
								}

							}
							else
							{
								SysMsg::showInfo("<span class='replace'>replacing file ".$to_path.$file." (if file hash matches original hash)</span>");
							}

						}
						else
						{

							if ($this->TEST == true)
							{
								echo "add file ".$to_path.$file."<br>";
							}
							else
							{

								if (copy($from_path.$ofile, $to_path.$file))
								{
									SysMsg::showInfo("adding file ".$to_path.$file." (replace hash check skipped, original file not found)");
								}
								else
								{
									SysMsg::showWarning("replacing file ".$to_path.$file." failed");
								}

							} // end if ($this->TEST == false)

						} // end if ($exists == true)

					} // end if ($check_for_permissions_only = false)
				}
			}
			closedir($handle);
		}
		else
		{
			unset($changes);
			return false;
		}

		unset($changes);
		return true;


	} // end function replaceFilesByHash

*/



	/**
	 * stripping comments and whitespaces from file content
	 *
	 *
	 */
	function cleanFileContent($p_content)
	{

		// fetching lines with doubleshlash comments
	//	$p_content=preg_replace("/[\x20\t]*[\/]{2}[\x20\S]*[\r\n]{1}/", "", $p_content);
		$p_content = preg_replace("/[\x20\t]*\/[\/]+.*[\r\n]{1,2}/", "", $p_content);

		// fetching lines with doublecross comments
	//	$p_content=preg_replace("/[\r\n]{1}[\x20\t]*[#]+[\x20\S]*[\r\n]{1}/", "\n", $p_content);
		$p_content = preg_replace("/[\r\n]{1,2}[\x20\t]*[#]+.*[\r\n]{1,2}/", "\n", $p_content);

		// fetching slash-star comment areas
		// execute AFTER double-slash- / double-cross-comments
		$p_content = preg_replace("@[\x20\t]*[/]+\*.*?\*/@s", "", $p_content);

		// fetching whitespace areas (line breaks, spaces, tabs) ending with at least one line break
	//	$p_content=preg_replace("/[\s]*[\r\n]{1}[\x20\t]*/", "\n", $p_content);
		$p_content = preg_replace("/[\s]*[\r\n]{1}[\x20\t]*/", "\n", $p_content);

		return $p_content;
	} // end function cleanFileContent




	/**
	 *
	 */
	function isUTF8($p_content)
	{
		$l_charset = mb_detect_encoding($p_content.'xyz', 'UTF-8, ISO-8859-1');

		if ($l_charset != 'UTF-8')
		{
			return false;
		}

		return true;
	} // end function isUTF8




	/**
	 *
	 */
	function uft8Encode($p_content, $p_ignore_charset = false)
	{
		if ($p_ignore_charset || $this->isUTF8($p_content) == false)
		{
			$p_content = utf8_encode($p_content);
		}

		return $p_content;
	} // end function uft8Encode





	/**
	 *
	 */
	function switchMsg($p_state = null)
	{
		if ($p_state !== null)
		{
			$this->silent	= empty($p_state);
		}

		return $p_state;
	} // end function switchMsg




	/**
	 *
	 */
	function getRBFileSource($p_path, $p_revision, $p_build)
	{
		if (empty($p_revision))
		{
			Plattform::error(null, 'revision is empty');
			return false;
		}

		if (empty($p_build))
		{
			Plattform::error(null, 'build is empty');
			return false;
		}


//		$l_rb_path = $p_path.'R'.$p_revision.'.B'.$p_build;
		$l_rb_path = MCP_PATH.'R'.$p_revision.'.B'.$p_build;
		if (!is_dir($l_rb_path))
		{
			return '';
		}
//		return $l_rb_path.DIRECTORY_SEPARATOR;

		$l_rb_path .= DIRECTORY_SEPARATOR.$p_path;
		if (!is_dir($l_rb_path))
		{
			return '';
		}

		return $l_rb_path;
	} // end method getRBFileSource



	/**
	 *
	 */
	function modifyLangFile ($p_path, $l_arr)
	{
		foreach($l_arr as $token => $settings)
		{
			$target = $settings['position'];
			$action = $settings['action'];

			if (!empty($settings['if_exists']))
			{
				$match = $settings['if_exists'];
				$exists = true;
			}
			else if (!empty($settings['if_not_exists']))
			{
				$match = $settings['if_not_exists'];
				$exists = false;
			}
			else
			{
				$match = 'define("'.$token.'"';
				$exists = true;
			}

			foreach($settings['wording'] as $lang => $wording)
			{
				$l_path = MC_ROOT_PATH.strtr($p_path, array(
					'{LANG}' => strtoupper($lang),
					'{lang}' => strtolower($lang),
				));

				$l_content_old = $this->readFileContent($l_path, true);

				if ( !is_array($wording))
				{
					$wording  = array('define("'.$token.'", "'.addcslashes($wording, '\\"').'");'."\n");
				}

				if ($exists)
				{
					if ($this->searchLine($l_content_old, $match) === false)
					{
						SysMsg::showInfo('token "'.$token.'" ('.$lang.') already exists (condition matched).');
						continue;
					}
				}
				else
				{
					if ($this->searchLine($l_content_old, $match))
					{
						SysMsg::showInfo('token "'.$token.'" ('.$lang.') already exists (condition matched).');
						continue;
					}
				}


				$l_position = $this->searchLine($l_content_old, $target);
				if ($l_position === false)
				{
					SysMsg::showWarning('position "'.$target.'" for token "'.$token.'" ('.$lang.') not found. entry {SKIPPED}.');
					continue;
				}

				switch($action)
				{
					case 'add' :
						$l_content_new = array_splice($l_content_old, ($l_position + 1), 0, $wording);
						break;

					case 'replace' :
						$l_content_new = array_splice($l_content_old, $l_position, 1, $wording);
						break;

					default :
						SysMsg::showError('unknown "'.$action.'" at token ('.$lang.'::'.$token.'). entry {SKIPPED}! (allowed actions: add|replace)');
						continue 2;
				}

				if ($this->writeFileContent($l_path, implode('', $l_content_new)))
				{
					SysMsg::showInfo('adding label for token "'.$token.'" ('.$lang.') {SUCCEED}.');
				}
				else
				{
					SysMsg::showError('adding label for token "'.$token.'" ('.$lang.') {FAILED}!');
				}
			} // end foreach $settings

		} // end foreach $l_arr

		return true;
	} // end method addLangToFile



} // end class File

