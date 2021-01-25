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

if (file_exists(dirname(__FILE__).'/config.php')) {
    require_once dirname(__FILE__). '/config.php';
}


/**
 * @author shippeli
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_moodle
extends ESRender_Module_ContentNode_Abstract {

	public function createInstance() {
		
		parent::createInstance();

        if (!file_exists(dirname(__FILE__).'/config.php')) {
            return true;
        }


        $logger = $this->getLogger();


		if(empty(MOODLE_BASE_DIR)) {
			$logger->error('MOODLE_BASE_DIR not set');
			return false;
		}

		if(empty(MOODLE_TOKEN)) {
			$logger->error('MOODLE_TOKEN not set');
			return false;
		}

		if(empty(MOODLE_CATEGORY_ID)) {
            $logger->error('MOODLE_CATEGORY_ID not set');
            return false;
        }

		$url = MOODLE_BASE_DIR . "/webservice/rest/server.php?wsfunction=local_edusharing_restore&moodlewsrestformat=json&wstoken=" . MOODLE_TOKEN;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, true );

		$params = array('nodeid'=> $this -> esObject->getObjectID(),'category' => MOODLE_CATEGORY_ID, 'title' => htmlentities($this -> esObject->getTitle()));

		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);
		curl_setopt($ch, CURLOPT_TIMEOUT, 90 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($ch);
		echo curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode >= 200 && $httpcode < 300 && strpos($resp, 'exception') === false) {
		    $resp = str_replace('<?php', '', $resp); // moodle reponse sometimes contains '<?php' for some reason
			$courseId = json_decode($resp);
			$logger->error('Restored course with id ' . $courseId);
            if(!is_numeric($courseId)) {
                $logger->error('No valid course id received');
                return false;
            }
			$this->cacheCourseId($courseId);
			return true;
		}
		$logger->error('Error restoring course to moodle - ' . $httpcode . ' ' . json_decode($resp)->exception);
		return false;
	}

	private function cacheCourseId($courseId) {
		$filename = $this -> esObject->getFilePath() . '.txt';
		$data = $courseId;
		file_put_contents($filename, $data);
	}

	/*
	 * Call moodle WS local_edusharing_handleuser
	 * create/fetch user
	 * enroll user
	 * retrieve token for login
	 * */
	private function getUserToken() {

		$logger = $this->getLogger();
		
		if(empty(MOODLE_BASE_DIR)) {
			$logger->error('MOODLE_BASE_DIR not set');
			return false;
		}
		
		if(empty(MOODLE_TOKEN)) {
			$logger->error('MOODLE_TOKEN not set');
			return false;
		}
				
		$url = MOODLE_BASE_DIR . "/webservice/rest/server.php?wsfunction=local_edusharing_handleuser&moodlewsrestformat=json&wstoken=" . MOODLE_TOKEN;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, true );
		$params = array('user_name' => htmlentities($this -> esObject -> getData() -> user->authorityName),
                        'user_givenname' => htmlentities($this -> esObject->getData()->user->user_givenname),
                        'user_surname' => htmlentities($this -> esObject->getData()->user->profile->lastName),
                        'user_email' => htmlentities($this -> esObject->getData()->user->profile->email),
                        'courseid' => $this->getCourseId(),
                        'role' => 'student'); // or role 'editingteacher'
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);
		curl_setopt($ch, CURLOPT_TIMEOUT, 90 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($ch);
		echo curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode >= 200 && $httpcode < 300 && strpos($resp, 'exception') === false) {
			$logger->info('retrieved user token');
			return json_decode($resp);
		}
		$logger->error('Error retrieving user token - ' . $httpcode . ' ' . json_decode($resp)->exception);
		return false;
	}

	public function dynamic() {

        if (!file_exists(dirname(__FILE__).'/config.php')) {
            echo parent::dynamic();
            return true;
            $Logger -> error('Error opening ' . dirname(__FILE__).'/config.php');
        }

		$id = $this->getCourseId();
		if($id === false) {
			return parent::dynamic();
		}
		$Template = $this -> getTemplate();
		$tempArray = array('url' => $this-> getForwardUrl(), 'previewUrl' => $this -> esObject->getPreviewUrl());
		
		if(Config::get('showMetadata'))
			$tempArray['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');
			 
		$tempArray['title'] = $this -> esObject->getTitle();
		echo $Template -> render('/module/moodle/dynamic', $tempArray);
		return true;
	}

    public function embed() {

        if (!file_exists(dirname(__FILE__).'/config.php')) {
            echo parent::dynamic();
            return true;
            $Logger -> error('Error opening ' . dirname(__FILE__).'/config.php');
        }

        $id = $this->getCourseId();
        if($id === false) {
            return parent::dynamic();
        }
        $Template = $this -> getTemplate();
        $tempArray = array('url' => $this-> getForwardUrl(), 'previewUrl' => $this -> esObject->getPreviewUrl());
        echo $Template -> render('/module/moodle/embed', $tempArray);
        return true;
    }
	
	protected function getCourseId() {
		$filename = $this -> esObject->getFilePath() . '.txt';
		$id = file_get_contents($filename);
		return $id;
	}
	
	protected function getForwardUrl() {
		return MOODLE_BASE_DIR . '/local/edusharing_webservice/forwardUser.php?token=' . urlencode($this-> getUserToken());
	}
	
	
	
}
