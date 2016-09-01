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

include_once ('../../conf.inc.php');


/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_qti
	extends ESRender_Module_ContentNode_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::refineInstanceConstraints()
	 */
	protected function refineInstanceConstraints($Sql, array $requestData, $requiredInstanceVersion)
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

			$Sql .= ' AND `ESOBJECT_LMS_ID` = \'' . $requestData['app_id'] . '\'';
			$Sql .= ' AND `ESOBJECT_COURSE_ID` = \'' . $requestData['course_id'] . '\'';
			$Sql .= ' AND `ESOBJECT_RESOURCE_ID` = \'' . $requestData['resource_id'] . '\'';
		}

		return $Sql;
	}

}
