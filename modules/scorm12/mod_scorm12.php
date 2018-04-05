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

class mod_scorm12
extends ESRender_Module_ContentNode_Abstract
{
    private $userSession;

    public function createInstance(array $requestData) {
        parent::createInstance($requestData);
    }

    private function pushToScormPlayer($requestData) {
        $url_path_str = $this->_ESOBJECT->getEsobjectFilePath();
        $file_path_str = '/my_file_path';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ''.$url_path_str.'');
        curl_setopt($ch, CURLOPT_PUT, 1);
        $fh_res = fopen($file_path_str, 'r');
        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file_path_str));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $curl_response_res = curl_exec ($ch);
        fclose($fh_res);


        /*
         * $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode >= 200 && $httpcode < 308) {
            curl_close($ch);
            return json_decode($res);
        }

        $error = curl_error($ch);
        curl_close($ch);
        throw new \Exception('Error creating content node HTTP STATUS ' . $httpcode . '. Curl error ' . $error, $httpcode);*/
    }

    private function getUserSession($requestData) {

    }

    public function inline(array $requestData) {
        $this -> pushToScormPlayer($requestData);
        $this -> getUserSession($requestData);
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/moodle/inline', array('url' => $this->userSession->url));
        return true;
    }

    public function dynamic(array $requestData) {
        $this -> pushToScormPlayer($requestData);
        $this -> getUserSession($requestData);
        $Template = $this -> getTemplate();
        $tempArray = array('url' => $this->userSession->url, 'previewUrl' => $this->_ESOBJECT->getPreviewUrl());
        if(Config::get('showMetadata'))
            $tempArray['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
        $tempArray['title'] = $this->_ESOBJECT->getTitle();
        echo $Template -> render('/module/moodle/dynamic', $tempArray);
        return true;
    }
}

