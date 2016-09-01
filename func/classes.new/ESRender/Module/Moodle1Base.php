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

require_once(dirname(__FILE__) . '/MoodleBase.php');

/**
 * A base-class for all modules which use a moodle-installation to render
 * content. As the importing process usually differs only in the process of
 * importing data only the abstract method _importData() has to be implemented
 * in subclasses.
 *
 *
 */
abstract class ESRender_Module_Moodle1Base
extends ESRender_Module_MoodleBase
{

	/**
	 * Retrieve a moodle-course-category. Returns false if an error occurs.
	 *
	 * @return stdClass
	 */
	protected function _getCourseCategory(array $requestData)
	{
		global $CFG;
		global $COURSE;
		global $DB;

		// search for category which is named like requesting lms AND has no parent
		$CourseCategory = get_record('course_categories', 'name', $requestData['app_id'], 'parent', 0);

		return $CourseCategory;
	}

	/**
	 * Creates a new moodle-course-category. Returns false if an error occurs.
	 *
	 * @return stdClass
	 */
	protected function _createCourseCategory(array $requestData)
	{
		global $CFG;
		global $COURSE;
		global $DB;

		$Logger = $this->getLogger();

		$CourseCategory = new stdClass();
		$CourseCategory->name = $requestData['app_id'];
		$CourseCategory->description = 'Kategorie für Kurse der node "'. $requestData['app_id'].'".';
		$CourseCategory->parent = 0;
		$CourseCategory->sortorder = 999;

		// Create a new category.
		if ( ! $CourseCategory->id = insert_record('course_categories', $CourseCategory))
		{
			$Logger->error('Error creating course-category "'.$CourseCategory->name.'".');
			return false;
		}

		if ( ! defined('CONTEXT_COURSECAT') )
		{
			$Logger->error('CONTEXT_COURSECAT not defined.', E_USER_ERROR);
			return false;
		}

		$CourseCategory->context = get_context_instance(CONTEXT_COURSECAT, $CourseCategory->id);
		mark_context_dirty($CourseCategory->context->path);

		$Logger->debug('Successfully created course-category "'.$CourseCategory->name.'" with id "'.$CourseCategory->id.'"');

		return $CourseCategory;
	}

	protected function _getCourse($courseId)
	{
		return get_record('course', 'id', $courseId);
	}

	/**
	 * Create a new moodle-user. Returns false if an error occurs.
	 *
	 * @return stdClass
	 */
	protected function _createUser(array $requestData)
	{
		global $CFG;

		$Logger = $this->getLogger();

		$user = new stdClass();

		$user->auth = 'manual';

		$username = $this->_buildUsername($requestData);
		$user->username = $username;

		$user->email = $requestData['user_email'];

		$user->firstname = $requestData['user_givenname'];
		$user->lastname = $requestData['user_surname'];

		$user->mnethostid = $CFG->mnet_localhost_id;
		$user->confirmed = 1;
		$user->password = hash_internal_user_password($username);

		$userId = insert_record('user', $user);

		if ( ! $userId )
		{
			$Logger->error('Error creating user "'.$user->username.'".');
			return false;
		}

		$user = get_record('user', 'id', $userId);
		if ( ! $user )
		{
			$Logger->error('Error reading created user.');
			$Logger->debug('User-id: ' . $userId);

			return false;
		}

		$Logger->info('Created moodle-user "'.$user->username.'" with id "'.$user->id.'".');

		return $user;
	}

	/**
	 * Test if given ESObject was previously rendered or has been deleted
	 * and does not exists in our moodle-instance anymore.
	 *
	 * @return bool
	 */
	public function instanceExists(ESObject $ESObject, array $requestData, $requiredInstanceVersion, $contentHash)
	{
		global $CFG;

		$Logger = $this->getLogger();

		// test if object was previously rendered.
		if ( ! parent::instanceExists($ESObject, $requestData, $requiredInstanceVersion, $contentHash) )
		{
			return false;
		}

		$Logger->debug('Testing for course-instance.');

		// to test if object exists in moodle-instance we have to
		// extract moodle's course-id from the ESOBJECT_PATH
		$ESObjectPath = $ESObject->getSubUri();
		if ( '' == $ESObjectPath )
		{
			$Logger->error('No ESOBJECT_PATH available.');

			throw new Exception('No ESOBJECT_PATH available.');
		}

		$RegEx = '/id=(?<MoodleCourseId>[0-9]+)$/';
		$Matches = array();

		$Result = preg_match($RegEx, $ESObjectPath, $Matches);
		if ( false == $Result )
		{
			$Logger->error('Could not determine course-id from ESObject-path "'.$ESObjectPath.'".');

			throw new Exception('Could not determine course-id from ESObject-path "'.$ESObjectPath.'".');
		}

		$MoodleCourseId = $Matches['MoodleCourseId'];

		// we assume the corresponding moodle-course exists if we find
		// an entry in the "course" table.
		$CourseExists = get_record('course', 'id', $MoodleCourseId);
		if ( ! $CourseExists )
		{
			$Logger->debug('Course-instance id "'.$Matches['MoodleCourseId'].'" does not exists.');

			throw new Exception('Course-instance not found.');
		}

		$Logger->debug('Course having id "'.$Matches['MoodleCourseId'].'" exists.');

		return true;
	}

	/**
	 *
	 */
	public function _assignRoleTeacher(stdClass $user, stdClass $course)
	{
		$Logger = $this->getLogger();

		$Logger->debug('User "'.$user->id.'" is teacher. Assigning role "editingteacher".');

		$targetRoleName = "editingteacher";

		$role = get_record('role', 'shortname', $targetRoleName);
		if ( ! $role )
		{
			$Logger->error('Required role not found.', E_USER_ERROR);
			return false;
		}

		if ( ! defined('CONTEXT_COURSE') )
		{
			$Logger->error('CONTEXT_COURSE not defined.', E_USER_ERROR);
			return false;
		}

		$context = get_context_instance(CONTEXT_COURSE, $course->id);
		if ( ! $context )
		{
			$Logger->error('Course-context (id: '.$course->id.') not found.');
			return false;
		}

		$RoleAssignmentId = role_assign(
				$role->id,
				$user->id,
				null,
				$context->id);
		if ( ! $RoleAssignmentId )
		{
			$Logger->error('Error assigning user "'.$user->id.'" to role "'.$targetRoleName.'" (id: '.$role->id.') for context "'.$context->id.'".');
			return false;
		}

		$Logger->debug('Successfully assigned user "'.$user->id.'" the role "editingteacher".');
	}

	/**
	 *
	 */
	public function _enrolUser(stdClass $user, stdClass $course)
	{
		$Logger = $this->getLogger();

		$Logger->debug('Attempting to enrol user in course.');

		// enroll user into newly created course
		$MoodleCourse = new MCMoodleCourse();
		if ( ! $MoodleCourse->addUserToCourse($course, $user) )
		{
			$Logger->error('Error enrolling user "'.$user->id.'" in course "'.$course->id.'".');
			return false;
		}

		$Logger->info('Successfully enrolled user "'.$user->id.'" in course "'.$course->id.'".');

		return true;
	}
    
        /**
     * Use moodle's login-mechanism to log in user.
     *
     * @param stdClass $user
     */
    protected function _loginUser(stdClass $user)
    {
        global $USER;

        /*
         * mhmm, helps, but i still don't know why exactly
        *
        * moodle::session_set_user($user) is said to setup the $USER object,
        * but it only stores given $user in $_SESSION['USER']. So it does NOT
        * setup a global variable $USER.
        */
        //      $USER = $user;
        complete_user_login($user);
    }

}
