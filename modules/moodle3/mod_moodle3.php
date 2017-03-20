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

require_once dirname(__FILE__). '/config.php';


/**
 * @author shippeli
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_moodle3
extends ESRender_Module_NonContentNode_Abstract {

	public function createInstance($requestData) {
		
		parent::createInstance($requestData);
		
		$url = MOODLE_BASE_DIR . "/webservice/rest/server.php?wsfunction=local_educopu_restore&moodlewsrestformat=json&wstoken=" . MOODLE_TOKEN;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, true );
		//48b38ed8-2f90-4025-ae9f-a6ecbd493bbc
		$params = array('nodeid'=> '48b38ed8-2f90-4025-ae9f-a6ecbd493bbc','category' => '1', 'title' => htmlentities('huhu'));
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec ( $ch );
		echo curl_error ($ch);
		$httpcode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		curl_close($ch);
		if ($httpcode >= 200 && $httpcode < 300 && strpos($resp, 'exception') === false) {
			echo '<a href="'.MOODLE_BASE_DIR.'/course/view.php?id='.json_decode(json_decode($resp))->id.'" target="_blank">Zum Kurs</a>';
			exit();
		}
		$r = json_decode($resp);
		echo $httpcode . ' ' . json_decode($resp)->exception;
		echo '<br/>Error pushing course<br/>';
		exit();
		
	}
	
	public function instanceExists($ESObject, $requestData, $contentHash) {
		
		//check locally
		//check in rendermoodle
		
	}
	
	public function display($requestData) {
		//get/show link
	}
	
	public function inline($requestData) {
		//get/show link
	}
	
	public function dynamic($requestData) {
		//get/show link
	}
	
	
	
}
