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
class mod_moodle
extends ESRender_Module_ContentNode_Abstract {

	public function createInstance(array $requestData) {
		
		parent::createInstance($requestData);
		$logger = $this->getLogger();
		
		
		if(empty(MOODLE_BASE_DIR)) {
			$logger->error('MOODLE_BASE_DIR not set');
			return false;
		}
		
		if(empty(MOODLE_TOKEN)) {
			$logger->error('MOODLE_TOKEN not set');
			return false;
		}
		
		$url = MOODLE_BASE_DIR . "/webservice/rest/server.php?wsfunction=local_edusharing_restore&moodlewsrestformat=json&wstoken=" . MOODLE_TOKEN;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, true );
		$params = array('nodeid'=> $requestData['object_id'],'category' => '1', 'title' => htmlentities($this->_ESOBJECT->getTitle()));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($ch);
		echo curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode >= 200 && $httpcode < 300 && strpos($resp, 'exception') === false) {
			$courseId = json_decode($resp);
			$logger->error('Restored course with id ' . $courseId);
			$this->cacheCourseId($courseId);
			return true;
		}
		$logger->error('Error restoring course to moodle - ' . $httpcode . ' ' . json_decode($resp)->exception);
		return false;
	}
	
	private function cacheCourseId($courseId) {
		$filename = $this->_ESOBJECT->getFilePath() . '.txt';
		$data = $courseId;
		file_put_contents($filename, $data);
	}
	
	public function instanceExists(ESObject $ESObject, array $requestData, $contentHash) {
		return parent::instanceExists($ESObject, $requestData, $contentHash);
	}
	
	public function display(array $requestData) {	
		$id = $this->getCourseId();
		if($id === false) {
			return parent::display($requestData);
		}
		
		header('Location: ' . $this-> getForwardUrl($requestData));
		return true;
	}
	
	
	/*
	 * Call moodle WS local_edusharing_handleuser
	 * create/fetch user
	 * enroll user
	 * retrieve token for login
	 * */
	private function getUserToken($requestData) {

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
		$params = array('user_name' => htmlentities($requestData['user_name']), 'user_givenname' => htmlentities($requestData['user_givenname']), 'user_surname' => htmlentities($requestData['user_surname']), 'user_email' => htmlentities($requestData['user_email']) , 'courseid' => $this->getCourseId(), 'role' => 'student'); // or role 'editingteacher'
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($ch);
		echo curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode >= 200 && $httpcode < 300 && strpos($resp, 'exception') === false) {
			$logger->error(json_decode($resp));
			return json_decode($resp);
		}
		
		$logger->error('Error retrieving user token - ' . $httpcode . ' ' . json_decode($resp)->exception);
		return false;
	}
	
	
	public function inline(array $requestData) {

		$id = $this->getId();
		
		if($id === false) {
			return parent::inline($requestData);
		}
		$Template = $this -> getTemplate();
		echo $Template -> render('/module/moodle/inline', array('url' => $this-> getForwardUrl($requestData)));
		return true;
	}
	
	public function dynamic(array $requestData) {
		global $accessToken;
				
		$id = $this->getCourseId();
		if($id === false) {
			return parent::dynamic($requestData);
		}
		$Template = $this -> getTemplate();
		$previewUrl = $this->_ESOBJECT->renderInfoLMSReturn->getRenderInfoLMSReturn->previewUrl;
		if(!empty($accessToken))
			$previewUrl .= '&accessToken=' . $accessToken;
		$tempArray = array('url' => $this-> getForwardUrl($requestData), 'previewUrl' => $previewUrl);
		
		if($requestData['dynMetadata'])
			$tempArray['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
			 
		$tempArray['title'] = $this->_ESOBJECT->getTitle();
		echo $Template -> render('/module/moodle/dynamic', $tempArray);
		return true;
	}
	
	protected function getCourseId() {
		$filename = $this->_ESOBJECT->getFilePath() . '.txt';
		$id = file_get_contents($filename);
		return $id;
	}
	
	protected function getForwardUrl($requestData) {
		return MOODLE_BASE_DIR . '/local/edusharing/forwardUser.php?token=' . urlencode($this-> getUserToken($requestData));
	}
	
	
	
}
