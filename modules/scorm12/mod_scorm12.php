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

require_once(dirname(__FILE__).'/config.php');
require_once (MOODLE_BASE_DIR.'/config.php');

global $CFG;

require_once (MC_LIB_PATH . '/MCMoodleCourse.class.php');
require_once ($CFG->dirroot.'/mod/scorm/lib.php');

require_once (dirname(__FILE__) . '/../../conf.inc.php');

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_scorm12
extends ESRender_Module_Moodle1Base
{

	protected function _createCourse(
		stdClass $CourseCategory)
	{
		global $CFG;

		$Logger = $this->getLogger();

		$courseName = 'CC_scorm_' . rand(100000, 999999);

		$newcourse = new stdClass();
		$newcourse->fullname = $courseName;
		$newcourse->shortname = $courseName;
		$newcourse->category = $CourseCategory->id;
		$newcourse->format = "scorm";

		// allow guest if course is not requested by an user of an LMS
		
		//does not work properly
		//always allow guest access
		
		//if ( 'LMS' != strtoupper($RemoteApplication->getType() ) )
		//{
			$newcourse->guest = 1;
		//}
		//else {
		//	$newcourse->guest = 0;
	//	}

		// create course we will add our object to
		$Logger->info('Creating course "'.$newcourse->fullname.'".');

		$MoodleCourse = new MCMoodleCourse();
		$newcourse2 = $MoodleCourse->createCourse($newcourse);
		if ( ! $newcourse2 )
		{
			$Logger->error('Error creating course.');
			return false;
		}

		// copy course-data into moodle-course-data-dir
		$moodleDataDirForCourse = $CFG->dataroot . DIRECTORY_SEPARATOR . $newcourse2->id;
		if ( ! mkdir( $moodleDataDirForCourse) )
		{
			$Logger->error('Failed to create folder "'.$moodleDataDirForCourse.'".');
			return false;
		}

		$Logger->debug('Created folder "'.$moodleDataDirForCourse.'".');

		$absSourceFilename = $this->render_path . DIRECTORY_SEPARATOR . $this->filename;

		$targetFilename = $this->filename . '.zip';
		$absTargetFilename = $moodleDataDirForCourse . DIRECTORY_SEPARATOR . $targetFilename;

		if ( ! copy($absSourceFilename, $absTargetFilename))
		{
			$Logger->error('Failed to copy course-data from "'.$absSourceFilename.'" to "'.$absTargetFilename.'".');
			return false;
		}

		$Logger->debug('Copied course-data "'.$absSourceFilename.'" to "'.$absTargetFilename.'".');

		unset($scorm);
		$scorm->modulname = 'scorm';
		$scorm->name = 'scorm';
		$scorm->summary = 'no summary given';
		$scorm->reference = $targetFilename;
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

		$instance = scorm_add_instance($scorm);

		if ( ! $instance ) {
			$Logger->error('Could not add a new instance of "'.$scorm->modulename.'" as "view.php?id='.$newcourse2->id.'"');
			return false;
		}

		$scorm->instance = $instance;

		// course_modules and course_sections each contain a reference
		// to each other, so we have to update one of them twice.

		if (! $scorm->coursemodule = add_course_module($scorm) ) {
			$Logger->error("Could not add a new course module");
			return false;
		}

		if (! $sectionid = add_mod_to_section($scorm) ) {
			$Logger->error("Could not add the new course module to that section");
			return false;
		}

		if (! set_field("course_modules", "section", $sectionid, "id", $scorm->coursemodule)) {
			$Logger->error("Could not update the course module with the correct section");
			return false;
		}

		if (!isset($scorm->visible)) {   // We get the section's visible field status
			$scorm->visible = get_field("course_sections","visible","id",$sectionid);
		}

		// make sure visibility is set correctly (in particular in calendar)
		set_coursemodule_visible($scorm->coursemodule, $scorm->visible);

		$DataArray['ESOBJECT_PATH'] = $this->_buildEsObjectPath($newcourse2->id);
		$this->_ESOBJECT->setData($DataArray);

		return $newcourse2;
	}

}

