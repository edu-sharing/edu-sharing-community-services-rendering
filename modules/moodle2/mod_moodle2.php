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
define('MOODLE_INTERNAL', true);

require_once (MOODLE_BASE_DIR.'/config.php');
global $CFG;


require_once(MOODLE_BASE_DIR . '/backup/util/includes/restore_includes.php');

require_once(dirname(__FILE__) . '/../../conf.inc.php');
require_once(MC_LIB_PATH."/../extern/pclZip/pclzip.lib.php");

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_moodle2
extends ESRender_Module_Moodle2Base
{

	/**
	 *
	 */
	protected function _createCourse(stdClass $CourseCategory, $guestAccess)
	{
		global $CFG;
		global $DB;
		global $USER;

		$Logger = $this->getLogger();

		if ( ! $this->_ESOBJECT )
		{
			throw new Exception('No ESOBJECT set.');
		}

		// create new course
		$file_name = $this->getCacheFileName();
		$zip_file = $file_path . '.zip';

		if ( ! copy($file_name, $zip_file) )
		{
			$Logger->error('Error renaming "'.$file_name.'" to "'.$zip_file.'".');
			return false;
		}

		$sname = substr( basename($zip_file), 0 , -4);
		$fhash = $sname.'_'.rand(100000, 999999);

		$newcourse = new stdClass();
		$newcourse->enrol = 'manual';
		$newcourse->fullname = "CC_".$fhash;
		$newcourse->shortname = "CC_".$fhash;
		$newcourse->category = $CourseCategory->id;

		$Logger->info('Creating course "'.$newcourse->fullname.'".');

		// store course in db
		$newcourse2 = $DB->get_record('course', array('shortname' => $newcourse->shortname));
		if ( ! $newcourse2 )
		{
			$Logger->debug('Course not found in db. Attempting to create a new course.');

			$newcourse2 = create_course($newcourse);
			if ( ! $newcourse2 ) {
				$Logger->error('Error creating course');
				$Logger->debug('Course - fullname: "'.$newcourse->fullname.'".');
				return false;
			}

			// allow guest if course is not requested by an user of an LMS
			if ($guestAccess)
			{
				$Logger->debug('Allowing guest-access as remote-app is not an LMS.');

				$enrol = enrol_get_plugin('guest');
                $enrol -> add_instance($newcourse2);
			}
			else {
				$Logger->debug('Using default guest-access-policy as remote-app is an LMS.');
			}
		}

		// extract backup-file
		if ( ! file_exists($zip_file) )
		{
			$Logger->error('Object-file "'.$zip_file.'" does not exists.');
			return false;
		}

		if ( ! file_exists($zip_file) )
		{
			$Logger->error('Object-file "'.$zip_file.'" not readable.');
			return false;
		}

		$tempdir = restore_controller::get_tempdir_name($newcourse2->id, $USER->id);
		$zip_temp = $CFG->dataroot . '/temp/backup/' . $tempdir;

        $fb = get_file_packer();
        if ( ! $fb->extract_to_pathname($zip_file, $zip_temp) )
        {
			$Logger->error('Error extracting moodle-backup-file "'.$zip_file.'" in temp-dir "'.$zip_temp.'".');
			return false;
        }

		// restore object from "backup", which is the zip we just created
		$Logger->info('Importing backup-file "'.$zip_file.'" for course "'.$newcourse2->id.'".');

		$rc = new restore_controller($tempdir, $newcourse2->id, backup::INTERACTIVE_NO, backup::MODE_GENERAL, 2, backup::TARGET_NEW_COURSE);
		if ( ! $rc->execute_precheck() ) {
			$Logger->error('Moodle restore-controller failed pre-check.');
      		$Logger->error( print_r($rc->get_precheck_results(), true) );

			return false;
		}

		$rc->execute_plan();
		if ( $rc->get_status() != backup::STATUS_FINISHED_OK ) {
			$Logger->error('Moodle restore-controller failed to execute restore-plan.');
			return false;
		}

		$Logger->info('Course "'.$newcourse2->id.'" for object "'.$this->_ESOBJECT->getObjectId().'" created successfully.');

		return $newcourse2;
	}



    //USE DEFAULT TEMPLATE
	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::inline()
	 
	protected function inline(
		EsApplication $RemoteApplication,
		array $requestData)
	{
		$Logger = $this->getLogger();

		$Template = $this->getTemplate();
		echo $Template->render('/module/moodle2/inline', array(
			'url' => 'http://renderservice/',
			'title' => 'Testitel',
		));

		return true;
	}*/

}
