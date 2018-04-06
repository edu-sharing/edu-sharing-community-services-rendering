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


class mod_scorm12
extends ESRender_Module_ContentNode_Abstract
{
    private $userSession = null;
    private $homeConfig = null;

    public function __construct($Name, ESRender_Application_Interface $RenderApplication, ESObject $p_esobject, Logger $Logger, Phools_Template_Interface $Template) {
        parent::__construct($Name, $RenderApplication, $p_esobject, $Logger, $Template);
        $application = new ESApp();
        $application -> getApp('esmain');
        $this -> homeConfig = $application -> getHomeConf();
    }

    private function pushToScormPlayer() {
        $scormid = hash_hmac('sha256', $this->_ESOBJECT->getObjectIdVersion(), $this -> homeConfig -> prop_array['private_key']);
        $url_path_str = SCORM_PLAYER_API . '/' . $scormid;
        $file_path_str = $this -> _ESOBJECT -> getFilePath();
        $ch = curl_init($url_path_str);
        $headers = array('Accept: application/json', 'Content-Type: multipart/form-data');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        $cfile = curl_file_create($file_path_str, 'application/zip', 'file');
        $fields = array('file' => $cfile);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode >= 200 && $httpcode < 308) {
            curl_close($ch);
            return true;
        }
        $error = curl_error($ch);
        curl_close($ch);
        $logger = $this->getLogger();
        $logger->error('Error pushing scorm package HTTP STATUS ' . $httpcode . '. Curl error ' . $error, $httpcode);
    }

    private function getUserSession($requestData) {
        $userId = hash_hmac('sha256', $requestData['user_name'], $this -> homeConfig -> prop_array['private_key']);
        $scormid = hash_hmac('sha256', $this->_ESOBJECT->getObjectIdVersion(), $this -> homeConfig -> prop_array['private_key']);
        $ch = curl_init(SCORM_PLAYER_API . '/' . $scormid . '/sessions/' . $userId);
        $headers = array('Accept: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode >= 200 && $httpcode < 308) {
            curl_close($ch);
            $this->userSession = json_decode($res);
            return true;
        }
        $error = curl_error($ch);
        curl_close($ch);
        $logger = $this->getLogger();
        $logger->error('Error creating content node HTTP STATUS ' . $httpcode . '. Curl error ' . $error, $httpcode);
    }

    private function getScormUrl() {
        if($this->userSession->body->sessions[0]->url)
            return $this->userSession->body->sessions[0]->url;
        return null;
    }

    public function inline(array $requestData) {

        $this -> pushToScormPlayer();
        $this -> getUserSession($requestData);
        $data = array();

        $data['url'] = $this->getScormUrl();
        if(empty($data['url'])) {
            parent::inline($requestData);
            return true;
        }

        if(ENABLE_METADATA_INLINE_RENDERING) {
            $metadata = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/inline');
            $data['metadata'] = $metadata;
        }
        $license = $this->_ESOBJECT->ESOBJECT_LICENSE;
        if(!empty($license)) {
            $data['license'] = $license -> renderFooter($this -> getTemplate());
        }
        $data['title'] = $this->_ESOBJECT->getTitle();
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/scorm12/inline', $data);
        return true;
    }

    public function dynamic(array $requestData) {
        $this -> pushToScormPlayer();
        $this -> getUserSession($requestData);
        $Template = $this -> getTemplate();
        $tempArray = array('url' => $this->getScormUrl(), 'previewUrl' => $this->_ESOBJECT->getPreviewUrl());
        if(Config::get('showMetadata'))
            $tempArray['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
        $tempArray['title'] = $this->_ESOBJECT->getTitle();
        echo $Template -> render('/module/scorm12/dynamic', $tempArray);
        return true;
    }
}

