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
require_once(MOODLE_BASE_DIR.'/config.php');

global $CFG;

require_once($CFG->libdir.'/xmlize.php');
require_once(MOODLE_BASE_DIR."/lib/accesslib.php");
require_once(MOODLE_BASE_DIR."/backup/lib.php");
require_once(MOODLE_BASE_DIR."/backup/restorelib.php");

require_once(dirname(__FILE__) . '/../../conf.inc.php');
require_once(MC_LIB_PATH . '/MCMoodleCourse.class.php');

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_moodle
extends ESRender_Module_Moodle1Base
{

	/*
	 *
	 */
	protected function _createCourse(
		stdClass $CourseCategory)
	{
		$Logger = $this->getLogger();

		// create object
		$file_name = $this->render_path . DIRECTORY_SEPARATOR . $this->filename;
		$zip_file = $file_name . '.zip';

		if ( ! copy($file_name, $zip_file) )
		{
			$Logger->error('Error renaming "'.$file_name.'" to "'.$zip_file.'".');
			return false;
		}

		$sname = substr( basename($zip_file), 0 , -4);
		$fhash = $sname.'_'.rand(100000, 999999);

		//.oO count the sections from the course
		require_once(MOODLE_BASE_DIR."/lib/pclzip/pclzip.lib.php");
		$l_file = new PclZip($zip_file);

		$x_file = $l_file->extract(PCLZIP_OPT_BY_NAME, "moodle.xml", PCLZIP_OPT_EXTRACT_AS_STRING);
		if ( ! $x_file )
		{
			$Logger->error('Error extracting zip-file "'.$zip_file.'".');
			return false;
		}

		$doc = new DOMDocument(1.0);
		if ( ! $doc->loadXML($x_file[0]['content']) )
		{
			$Logger->error('Error loading xml-document moodle.xml.');
			return false;
		}

		$nodes = $doc->getElementsByTagName('SECTION');
		if ( 0 == $nodes->length )
		{
			$Logger->error('Could not find nodes by tag-name "SECTION".');
			return false;
		}

		$sections = $nodes->length;

		//.oO get format from XML (lams, scorm, social, topics, weeks, weekscss)
		$nodes = $doc->getElementsByTagName('FORMAT');
		if ( 0 == $nodes->length )
		{
			$Logger->error('Could not find nodes by tag-name "FORMAT".');
			return false;
		}

		$format = ($nodes->length == 1) ? $nodes->item(0)->nodeValue : 'topics';

		$newcourse = new stdClass();
		$newcourse->fullname = "CC_".$fhash;
		$newcourse->shortname = "CC_".$fhash;
		$newcourse->category = $CourseCategory->id;
		$newcourse->numsections = $sections;
		$newcourse->format = $format;

		// allow guest if course is not requested by an user of an LMS
		if ( 'REPOSITORY' == strtoupper($RemoteApplication->getType() ) )
		{
			$Logger->debug('Allowing guest-access as remote-app is not an LMS.');
			$newcourse->guest = 1;
		}
		else {
			$Logger->debug('Disallowing guest-access as remote-app is an LMS.');
			$newcourse->guest = 0;
		}

		// create course we will add our object to
		$Logger->info('Creating course "'.$newcourse->fullname.'".');

		$MoodleCourse = new MCMoodleCourse();
		$newcourse2 = $MoodleCourse->createCourse($newcourse);
		if ( ! $newcourse2 )
		{
			$Logger->error('Error creating course "'.$newcourse->fullname.'".');
			return false;
		}

		// restore object from "backup", which is the zip we just created
		$Logger->info('Importing backup-file "'.$zip_file.'" for course "'.$newcourse2->id.'".');
		if ( ! import_backup_file_silently($zip_file,$newcourse2->id,$emptyfirst=true) )
		{
			$Logger->error('Error importing moodle-course.');
			return false;
		}

		$Logger->info('Course "'.$newcourse2->id.'" imported successfully.');

		return $newcourse2;
	}

}

