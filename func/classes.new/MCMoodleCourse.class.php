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
//	require_once('../../config.php');
//	require_once($CFG->libdir.'/adminlib.php');
//	require_once($CFG->dirroot.'/user/profile/lib.php');

 	//global $CFG, $COURSE;
	//static $cshortname;

/*
MCMoodleCourse Klasse

 createCourse
 deleteCourse

 getCourseData
 setCourseData

 getCourseUser
 setCourseUser(Array mit User_ids)

  ["MAX_FILE_SIZE"]=>  string(9) "120586240"
  ["category"]=>  string(1) "1"
* ["fullname"]=>  string(8) "Auf Kurs"
* ["shortname"]=>  string(4) "AK01"
  ["idnumber"]=>  string(4) "0815"
  ["summary"]=>  string(13) " Just A Test "
  ["format"]=>  string(5) "weeks"
  ["numsections"]=>  string(2) "10"
  ["startdate"]=>  string(10) "1234393200"
  ["hiddensections"]=>  string(1) "0"
  ["newsitems"]=>  string(1) "5"
  ["showgrades"]=>  string(1) "1"
  ["showreports"]=>  string(1) "0"
  ["maxbytes"]=>  string(9) "120586240"
  ["metacourse"]=>  string(1) "0"
  ["enrol"]=>  string(0) ""
  ["defaultrole"]=>  string(1) "0"
  ["enrollable"]=>  string(1) "1"
  ["enrolstartdate"]=>  int(0)
  ["enrolstartdisabled"]=>  string(1) "1"
  ["enrolenddate"]=>  int(0)
  ["enrolenddisabled"]=>  string(1) "1"
  ["enrolperiod"]=>  string(1) "0"
  ["expirynotify"]=>  string(1) "0"
  ["notifystudents"]=>  string(1) "0"
  ["expirythreshold"]=>  string(6) "864000"
  ["groupmode"]=>  string(1) "0"
  ["groupmodeforce"]=>  string(1) "0"
  ["visible"]=>  string(1) "0"
  ["enrolpassword"]=>  string(0) ""
  ["guest"]=>  string(1) "0"
  ["lang"]=>  string(0) ""
  ["restrictmodules"]=>  string(1) "0"
  ["role_1"]=>  string(0) ""
  ["role_2"]=>  string(0) ""
  ["role_3"]=>  string(0) ""
  ["role_4"]=>  string(0) ""
  ["role_5"]=>  string(0) ""
  ["role_6"]=>  string(0) ""
  ["role_7"]=>  string(0) ""
  ["id"]=>  string(1) "3"

  ["teacher"]=>  string(10) "Trainer/in"
  ["teachers"]=>  string(13) "Trainer/innen"
  ["student"]=>  string(13) "Teilnehmer/in"
  ["students"]=>  string(16) "Teilnehmer/innen"
  ["password"]=>  string(0) ""
  ["timemodified"]=>  int(1234453735)


groupmode(s): http://docs.moodle.org/en/Groups#Group_modes

0-No groups (There are no sub groups, everyone is part of one big community.)
1-Separate groups (Each group can only see their own group, others are invisible.)
2-Visible groups (Each group works in their own group, but can also see other groups. (The other groups' work is read-only.) )



 * A convenience function to take care of the common case where you
 * just want to enrol someone using the default role into a course
 *
 * @param object $course
 * @param object $user
 * @param string $enrol - the plugin used to do this enrolment
// function enrol_into_course($course, $user, $enrol) {



 if (enrol_into_course($course, $USER, 'manual')) {
                // force a refresh of mycourses
                unset($USER->mycourses);
                if ($groupid !== false) {
                    if (!groups_add_member($groupid, $USER->id)) {
                        print_error('couldnotassigngroup');
                    }
                }
            } else {
                print_error('couldnotassignrole');
            }


*/





class MCMoodleCourse
{

	//protected $name;
	/**
	 * constructor
	 */
	function __construct() //$p_config
	{
    global $CFG, $COURSE;

    $this->_COURSE = $COURSE;
    //$this->name="";

		return true;
  } // constructor end


	/**
	 *
	 * example:
	 *	$newcourse = new object();
	 *	$newcourse->fullname = "GhostCourse";
	 *	$newcourse->shortname = "GHOST001";
	 *	$newcourse->category = 1;
	 *	$newcourseid = MCMoodleCourse::createCourse($newcourse);
	 *
	 * returns:
	 *	new course id
	 *
	 */
	public function createCourse($p_coursedata)
	{
		global $CFG;

		if( ! MCMoodleCourse::existsCourse($p_coursedata->shortname) )
		{
			// create
			if ($course = create_course($p_coursedata))
			{
				$context = get_context_instance(CONTEXT_COURSE, $course->id);
				//role_assign($CFG->creatornewroleid, $USER->id, 0, $context->id);  // add manager.... need research.. which teacher we should use?
				//role_assign($CFG->creatornewroleid, 15, 0, $context->id);
				//mark_context_dirty($context->path);
		
				return $course;
			}
		}
		
		return false;
	}

	/**
	 *
	 */
	public function deleteCourse($c_courseid)
	{
		$delreturn = delete_course($c_courseid);
		return $delreturn;
	}

/**
 *
 */
  public function existsCourse($c_shortname) {
      // does a course with the given shortname exist?

      //$cshortname = mystr($c_shortname);

	    if (!$tmpcourse = get_record('course', 'shortname', $c_shortname)) {
	        return false;
	    } else {
	    	return true;
	    }
  } // eof method existsCourse


/**
 * Enrol user to course
 */
	public function addUserToCourse($c_courseid, $c_userid) {
		if (enrol_into_course($c_courseid, $c_userid, 'manual')) {
			return true;
		} else {
			return false;
		}
	} // eof method addUserToCourse



/**
 *
 * example:
 *	$newcat = new object();
 *	$newcat->name = "GHOSTCAT";
 *	$newcat->description = "nothing to describe";
 *	$newcat->parent = 9; // id of the parent category
 *	$newcat_id = MCMoodleCourse::addCategory($newcat);
 *
 * returns:
 *  new category id
 *
 */
	public function addCategory($newcatobj) {
		global $CFG;

		if(!$newcatobj->id = insert_record("course_categories", $newcatobj)) {
			return false;
		}

		$newcatobj->path = MCMoodleCourse::getCategoryPath($newcatobj->id);
		$newcatobj->depth = substr_count($newcatobj->path, '/');
		$newcatobj->sortorder = 999;
		update_record('course_categories', $newcatobj);

		// set the context ...
    $newcatobj->context = get_context_instance(CONTEXT_COURSECAT, $newcatobj->id);
    mark_context_dirty($newcatobj->context->path);

		return $newcatobj;
	} // eof method addCategory


/**
 *
 */
	public function removeCategory($catid) {
		global $CFG;

    category_delete_full($catid, false);
    //category_delete_move($deletecat, $data->newparent, true);

    return false;
	}// eof method removeCategory


/**
 *
 * recursive method to get the path of a moodle category...
 * call with id=9, get something like /3/4/6/7/8/9
 *
 */
	public static function getCategoryPath($c_catid, $c_path = '') {
		global $CFG;
    if (!$res = get_record_sql("SELECT id,parent FROM {$CFG->prefix}course_categories WHERE id='".$c_catid."'")) {
        return $c_path;
    } else {
			$c_path = "/".$res->id.$c_path;
			if ( $res->parent != 0) {
				$c_path = MCMoodleCourse::getCategoryPath($res->parent, $c_path);
			}
			return $c_path;
    }
	} // eof method addcategory


} //   eof class MCMoodleCourse







/*
         $rs = get_recordset_sql ("SELECT id, userid, courseid
                                        FROM {$CFG->prefix}user_lastaccess
                                       WHERE courseid != ".SITEID."
                                         AND timeaccess < $cuttime ");
            while ($assign = rs_fetch_next_record($rs)) {
                if ($context = get_context_instance(CONTEXT_COURSE, $assign->courseid)) {
                    if (role_unassign(0, $assign->userid, 0, $context->id)) {
                        mtrace("Deleted assignment for user $assign->userid from course $assign->courseid");
                    }
                }
            }
            rs_close($rs);
*/

