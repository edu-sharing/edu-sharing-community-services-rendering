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

require_once(dirname(__FILE__) . '/Base.php');
require_once (MC_LIB_PATH . '/MCMoodleCourse.class.php');

/**
 * A base-class for all modules which use a moodle-installation to render
 * content. As the importing process usually differs only in the process of
 * importing data only the abstract method _importData() has to be implemented
 * in subclasses.
 *
 *
 */
abstract class ESRender_Module_MoodleBase
extends ESRender_Module_ContentNode_Abstract
{

	/**
	 * Placeholder to be implemented in subclasses to specialize importing of
	 * different course-types like Moodle- or SCORM-courses.
	 *
	 * @param EsApplication $RemoteApplication
	 * @param stdClass $CourseCategory
	 * @return bool
	 */
	abstract protected function _createCourse(stdClass $CourseCategory, $guestAccess);

	/**
	 * Placeholder to be filled by specialzed moodle-module for fetching the
	 * requested course-instance.
	 *
	 * @param int $courseId
	 *
	 * @return stdClass
	 */
	abstract protected function _getCourse($courseId);

	/**
	 * @return string
	 */
	protected function _buildEsObjectPath($MoodleCourseId)
	{
		$ESObjectPath = '/course/view.php?id='.$MoodleCourseId;
		return $ESObjectPath;
	}

	/**
	 * Test if user has teacher's permission for given course.
	 *
	 */
	protected function _isTeacherForCourse(EsApplication $Application, array $requestData)
	{
		$Logger = $this->getLogger();

		if ( empty($Application->prop_array['type']) )
		{
			$Logger->info('No "type" configured, deniying teacher-access.');
			return false;
		}

		if ( 'LMS' != strtoupper($Application->prop_array['type']) )
		{
			$Logger->info('Configured type not "LMS", deniying teacher-access.');
			return false;
		}

		try
		{
			$PermissionServiceWsdl = $Application->prop_array['permissionwebservice_wsdl'];
			if ( '' == $PermissionServiceWsdl )
			{
				$Logger->error('No permission-service configured.');
				return false;
			}

			$HasTeachingPermission = $Application->prop_array['hasTeachingPermission'];
			if ( '' == $HasTeachingPermission )
			{
				$Logger->error('Denying teaching-permissions, as NO teaching-permission configured.');
				return false;
			}

			$Logger->debug('Calling permission-service "'.$PermissionServiceWsdl.'".');

			$SoapClientParams = array();
			if ( defined('USE_HTTP_PROXY') && USE_HTTP_PROXY )
			{
				$SoapClientParams = array(
						'proxy_host' => HTTP_PROXY_HOST,
						'proxy_port' => HTTP_PROXY_PORT,
						'proxy_login' => HTTP_PROXY_USER,
						'proxy_password' => HTTP_PROXY_PASS,
				);
			}

			$SoapClient = new SoapClient($PermissionServiceWsdl, $SoapClientParams);

			try
			{
				// check primary role
				$Logger->debug('Calling LMS::getPrimaryRole().');
				$Response = $SoapClient->getPrimaryRole(array(
						'userid'	=> $requestData['user_name'],
						'session'   => $requestData['session']
				));
			}
			catch (Exception $exception)
			{
				$Logger->debug('Catched exception from call to getPrimaryRole().');
				$Logger->debug('Given message: "'.$exception->getMessage().'".');
			}

			// check permission
			$Logger->debug('Calling LMS::getPermission().');
			$Response = $SoapClient->getPermission(array(
					'session'   => $requestData['session'],
					'courseid'  => $requestData['course_id'],
					'action'	=> $HasTeachingPermission,
					'resourceid'=> '-1' // don't bother querying for a specific resource, as we're interested in teaching-permissions for the whole course
			));

			if ( true == $Response->getPermissionReturn )
			{
				$Logger->debug('User has teaching-permission.');
				return true;
			}
		}
		catch(SoapFault $SoapFault)
		{
			$Logger->error('Error executing SOAP::getPermission().');
			$Logger->error('Given message: "'.$SoapFault->getMessage().'"');
		}

		$Logger->debug('User does not have teaching-permission.');
		return false;
	}

	/**
	 * Create a new moodle-user. Returns false if an error occurs.
	 *
	 * @param array $requestData
	 *
	 * @return stdClass
	 */
	abstract protected function _createUser(array $requestData);

	/**
	 *
	 */
	abstract protected function _assignRoleTeacher(stdClass $user, stdClass $course);

	/**
	 *
	 */
	abstract protected function _enrolUser(stdClass $user, stdClass $course);

	/**
	 * As the course-id is stored as parameter inside the $ESObject->getSubUri()
	 * this helper-method extracts it for us.
	 *
	 * @return int
	 */
	protected function _extractCourseIdFromObjectPath($ObjectPath) {
		$RegEx = '/id=(?<MoodleCourseId>[0-9]+)$/';
		$Matches = array();

		$Result = preg_match($RegEx, $ObjectPath, $Matches);
		if ( false == $Result )
		{
			$Logger->error('Error grepping moodle-course-id from ESObject-path "'.$ESObjectPath.'".');
			return false;
		}

		return $Matches['MoodleCourseId'];
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::refineInstanceConstraints()
	 */
	protected function refineInstanceConstraints($Sql, array $requestData)
	{
		/*
		 * if course-id given, a resource-id must be provided as the same object
		* might exist as two different resources in the same course.
		*
		* if a resource-id is given but no course-id specified, its just an
		* invalid request as repositories, which don't submit a course-id shall
		* not request a resource-id
		*/
		if ( ( ! empty($requestData['course_id']) ) OR ( ! empty($requestData['resource_id']) ) )
		{
			if ( empty($requestData['course_id']) OR empty($requestData['resource_id']) )
			{
				$Logger->error('Invalid state: Course- or resource-identifier specified, but not both of them.');
				throw new Exception('Required resource- or course-identifier NOT found.');
			}

			$Sql .= ' AND '.$this->DB->quoteIdentifier('ESOBJECT_LMS_ID').' = '.$this->DB->quote($requestData['app_id'], 'text');
			$Sql .= ' AND '.$this->DB->quoteIdentifier('ESOBJECT_COURSE_ID').' = '.$this->DB->quote($requestData['course_id'], 'text');
			$Sql .= ' AND '.$this->DB->quoteIdentifier('ESOBJECT_RESOURCE_ID').' = '.$this->DB->quote($requestData['resource_id'], 'text');
		}

		return $Sql;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::createInstance()
	 */
	final public function createInstance(array $requestData)
	{
		if ( ! parent::createInstance($requestData) )
		{
			return false;
		}

		global $CFG;
		global $DB;
		global $USER;
		global $SESSION;

		$Logger = $this->getLogger();

		ob_start();

		/**
		 * Define to avoid moodle-developer's bad idea to output something
		 * during course-restoration as we cannot send headers afterwards.
		 */
		if ( ! defined('RESTORE_SILENTLY_NOFLUSH') )
		{
			define('RESTORE_SILENTLY_NOFLUSH', 1);
		}

		/*
		 * Create and login user first a.k.a. there has to be a user who is
		* restoring this course (usually the case when using the GUI).
		*/
		$user = null;
        $guestAccess = false;
		if ($requestData['app_id'] != $requestData['rep_id'] )
		{
			$username = $this->_buildUsername($requestData);

			$user = get_complete_user_data('username', $username);
			if ( ! $user )
			{
				$Logger->debug('User not found. Attempting to create.');

				$user = $this->_createUser($requestData);
				if ( ! $user )
				{
					$Logger->error('Error creating user "'.$user->username.'".');
					// 					ob_end_clean();
					return false;
				}
			}
		}
		else
		{
			// taken from moodle-1.9.12/lib/moodlelib.php
			/// Check if the guest user exists.  If not, create one.
			$user = get_complete_user_data('username', 'guest', $CFG->mnet_localhost_id);
			if (! $user )
			{
				$user = create_guest_record();
				if ( ! $user )
				{
					$Logger->error('Could not create guest user record !!!');
					return false;
				}
                $guestAccess = true;
			}

		}

		if ( ! $user )
		{
			$Logger->error('Could not determine corresponding user.');
			// 			ob_end_clean();
			return false;
		}

		$this->_loginUser($user);

		// create a category for course
		$CourseCategory = $this->_getCourseCategory($requestData);
		if ( ! $CourseCategory )
		{
			$Logger->error('Error reading course-category. Attempting to create one.');

			$CourseCategory = $this->_createCourseCategory($requestData);
			if ( ! $CourseCategory )
			{
				$Logger->error('Error creating course-category.');
				// 				ob_end_clean();
				return false;
			}
		}

		$Logger->info('Successfully created course-category.');

		// import course-data
		$course = $this->_createCourse($CourseCategory, $guestAccess);
		if ( ! $course )
		{
			$Logger->error('Error creating course.');
			// 			ob_end_clean();
			return false;
		}

		$DataArray['ESOBJECT_PATH'] = $this->_buildEsObjectPath($course->id);
		$this->_ESOBJECT->setData($DataArray);

		// 		ob_end_clean();

		$Logger->info('Successfully created an object-instance.');

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::display()
	 */
	protected function display(array $requestData)
	{
		global $CFG;
		global $DB;
		global $USER;
		global $SESSION;

		$Logger = $this->getLogger();

		ob_start();

		// logout current user -> clear cookies
		// 		require_logout();

		$m_path = $CFG->wwwroot;
		$m_path .= $this->_ESOBJECT->getSubUri();

		$m_mimeType = $this->_ESOBJECT->getMimeType();
		$m_file = $this->_ESOBJECT->getFilePath();
		$m_name = $this->_ESOBJECT->getTitle();

        $RemoteApplication = new ESApplication(MC_BASE_DIR . '/conf/esmain/app-' . $requestData['app_id'] . '.properties.xml');
        $RemoteApplication -> readProperties();
		/*
		 * if remote-system is a LMS we'll "track" the LMS's users to
		* personalize the experience and to allow for SCORM/QTI-tests
		*/
		$user = null;
		if ( 'LMS' == strtoupper($RemoteApplication->getType() ) )
		{
			$courseId = $this->_extractCourseIdFromObjectPath($this->_ESOBJECT->getSubUri());
			if ( ! $courseId )
			{
				$Logger->error('No course-id extracted.');
				return false;
			}

			$course = $this->_getCourse($courseId);

			// create user
			$username = $this->_buildUsername($requestData);

			$user = get_complete_user_data('username', $username);
			if ( ! $user )
			{
				$Logger->debug('User "'.$username.'" not found. Attempting to create course-user.');

				$user = $this->_createUser($requestData);
				if ( ! $user )
				{
					$Logger->error('Error creating user "'.$user->username.'".');
					return false;
				}
			}

			$Logger->info('Using user with id "'.$user->id.'".');

			$this->_enrolUser($user, $course);

			// check if user shall be teacher or student
			if ( $this->_isTeacherForCourse($RemoteApplication, $requestData) )
			{
				$this->_assignRoleTeacher($user, $course);
			}
		}
		else
		{
			// taken from moodle-1.9.12/lib/moodlelib.php
			/// Check if the guest user exists.  If not, create one.
			$user = get_complete_user_data('username', 'guest', $CFG->mnet_localhost_id);
			if ( ! $user )
			{
				$user = create_guest_record();
				if ( ! $user ) {
					$Logger->error('Could not create guest user record !!!');
					return false;
				}
			}

			/*
			 * Remember to enable automatic guest login in moodle.
			*
			* Non-LMS systems only get guest-access.
			*/
			$m_path .= '&username=guest';
		}

		if ( ! $user )
		{
			$Logger->error('Could not determine corresponding user.');
			return false;
		}

		$headers_sent_file = '';
		$headers_sent_line = 0;
		if ( headers_sent($headers_sent_file, $headers_sent_line) )
		{
			error_log('Headers already sent. output started in file "'.$headers_sent_file.'" on line "'.$headers_sent_line.'".');
			return false;
		}

		$this->_loginUser($user);

		$Logger->info('Redirecting to location: "'.$m_path.'".');

		header('Location: '.$m_path, true, 303);

		$Logger->info('Successfully processed object.');

		return true;
	}
}

