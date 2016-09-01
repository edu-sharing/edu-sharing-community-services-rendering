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
session_destroy();

require_once(dirname(__FILE__).'/config.php');
require_once (MOODLE_BASE_DIR.'/config.php');

global $CFG;

require_once (MC_LIB_PATH . '/MCMoodleCourse.class.php');
include_once ("../../modules/moodle/mod/scorm/lib.php");

require_once (dirname(__FILE__) . '/../../conf.inc.php');

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_scorm2004
extends ESRender_Module_MoodleBase
{

	/**
	 *
	 */
	final public function createInstance(
		array $requestData)
	{
		if ( ! parent::createInstance($requestData) )
		{
			return false;
		}

		$fileandpath_src = $this->render_path.DIRECTORY_SEPARATOR.$this->filename;

//		$zip_file = $file_path.'.zip';

		$sname = "scorm"; //substr($fname, 0 , -4);
		$fhash = $sname.'_'.rand(100000, 999999);

		$moodc = new MCMoodleCourse();
		$newcourse = new stdClass();
		$newcourse->fullname = "CC_".$fhash;
		$newcourse->shortname = "CC_".$fhash;
		$newcourse->category = 1;
		$newcourse->format = "scorm";
		$newcourse->guest = 1; // autologin guest if course entered by not loggedin user
		$newcourse2 = $moodc->createCourse($newcourse);

//		$fname=$this->_ESOBJECT->AlfrescoNode->properties['{http://www.alfresco.org/model/system/1.0}node-uuid'].'.zip';
		$fname = $this->filename.'.zip';

		$fileandpath_dst = dirname(getenv("DOCUMENT_ROOT")).'/esmoodledata/'.$newcourse2->id.'/'.$fname; // maybe add unique token to filename?

		//echo($fileandpath_src.'----'.$fileandpath_dst);

		if (!mkdir(dirname($fileandpath_dst))) {
			$Logger->error('Failed to create folder "'.$fileandpath_dst.'".');
			return false;
		}

		if (!copy($fileandpath_src, $fileandpath_dst)) {
			$Logger->error('failed to copy file from "'.$fileandpath_src.'" to "'.$fileandpath_dst.'".');
			return false;
		}

		unset($scorm);
		$scorm->modulname = 'scorm';
	$scorm->name = 'scorm';
	$scorm->summary = 'no summary given';
	$scorm->reference = $fname;
	$scorm->grademethod = '0';
	$scorm->maxgrade = '0';
	$scorm->maxattempt = '3';
	$scorm->whatgrade = '0';
	$scorm->mform_showadvanced_last = '';
	$scorm->width = '100';
	$scorm->height = '500';
	$scorm->popup = '0';
	$scorm->skipview = '1';
	$scorm->hidebrowse = '0';
	$scorm->hidetoc = '0';
	$scorm->hidenav = '0';
	$scorm->auto = '0';
	$scorm->updatefreq = '0';
	$scorm->datadir = '';
	$scorm->pkgtype = '';
	$scorm->launch = '';
	$scorm->redirect = 'yes';
	$scorm->redirecturl = '../course/view.php?id='.$newcourse2->id;
	$scorm->visible = '1';
	$scorm->cmidnumber = '';
	$scorm->gradecat = ''; // 40..41...42... grades...hmmmm... .. .
	$scorm->course = $newcourse2->id;
	$scorm->coursemodule = '';
	$scorm->section = '0';
	$scorm->module = MOODLE_MODULE_SCORM_ID;
	$scorm->modulename = 'scorm';
	$scorm->instance = '';
	$scorm->add = 'scorm';
	$scorm->update = '0';
	$scorm->return = '0';
	$scorm->groupingid = '0';
	$scorm->groupmembersonly = '0';
	$scorm->groupmode = '0';

		$return = scorm_add_instance($scorm);

		if (!$return) {
			$Logger->error("Could not add a new instance of $scorm->modulename", "view.php?id=$course->id");
			return false;
		}

		$scorm->instance = $return;

	// course_modules and course_sections each contain a reference
	// to each other, so we have to update one of them twice.

	if (! $scorm->coursemodule = add_course_module($scorm) ) {
		error("Could not add a new course module");
	}
	if (! $sectionid = add_mod_to_section($scorm) ) {
		error("Could not add the new course module to that section");
	}

	if (! set_field("course_modules", "section", $sectionid, "id", $scorm->coursemodule)) {
		error("Could not update the course module with the correct section");
	}

	if (!isset($scorm->visible)) {   // We get the section's visible field status
		$scorm->visible = get_field("course_sections","visible","id",$sectionid);
	}
	// make sure visibility is set correctly (in particular in calendar)
	set_coursemodule_visible($scorm->coursemodule, $scorm->visible);


//	global $CFG;
//	$DataArray['ESOBJECT_PATH']=  $CFG->wwwroot.'/course/view.php?id='.$newcourse2->id ;
	$DataArray['ESOBJECT_PATH']=  '/course/view.php?id='.$newcourse2->id ;

//	$DataArray['ESOBJECT_FILE_PATH']=  $fileandpath_src;
//	$DataArray['ESOBJECT_ESMODULE_ID']=  $this->_ESOBJECT->ESModule->getModuleId();

	$this->_ESOBJECT->setData($DataArray);

		return true;
	}


	final public function process(
		$p_kind,
		array $requestData)
	{
		global $CFG;

		$m_path = $CFG->wwwroot.$this->_ESOBJECT->getSubUri();

		$m_mimeType = $this->_ESOBJECT->getMimeType();
		$m_file = $this->_ESOBJECT->getFilePath();
		$m_name = $this->_ESOBJECT->getTitle();

	  	$obj_module = $this->_ESOBJECT->getModule();
		$p_kind = $obj_module->getConf('defaultdisplay', $p_kind);

		switch($p_kind)
		{
			case ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD:
				header('Content-type: '.$m_mimeType);
				header('Content-Disposition: attachment; filename="'.$m_name.'"');
				readfile($m_file);
			break;

			case ESRender_Application_Interface::DISPLAY_MODE_INLINE:
			break;

			case ESRender_Application_Interface::DISPLAY_MODE_WINDOW:
				redirect($m_path);
			break;

			default:
				$content = "news.php";
			break;
		}

		return true;
	}

}

