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

require_once (dirname(__FILE__).'/Plattform.php');

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class ESModule {

	protected $ESMODULE_ID;
	protected $ESMODULE_NAME;
	protected $ESMODULE_DESC;
	protected $ESMODULE_TYPE;
	protected $ESMODULE_URI;
	protected $ESMODULE_CONF;

	protected $parsed_conf;

	/**
	 *
	 */
	public function __construct($p_id = null)
	{
		//parent::__construct();

		$this->ESMODULE_ID  =  0;
		$this->ESMODULE_NAME = '';
		$this->ESMODULE_DESC = '';

		if ($p_id)
		{
			$this->setModuleID($p_id);
		}

		return true;
	}

	final public function setModuleID($p_id)
	{

		$pdo = RsPDO::getInstance();
        
        try {
    		$sql = $pdo -> formatQuery('SELECT `ESMODULE_ID`,`ESMODULE_NAME`,`ESMODULE_DESC` FROM `ESMODULE` WHERE `ESMODULE_ID` = :modid');
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':modid', $p_id, PDO::PARAM_INT);
            $stmt -> execute();
            $result = $stmt -> fetchObject();
            
            if(empty($result))
                return false;
            
    		$this -> ESMODULE_ID   =  $result -> ESMODULE_ID;
    		$this -> ESMODULE_NAME =  $result -> ESMODULE_NAME;
    		$this -> ESMODULE_DESC =  $result -> ESMODULE_DESC;

		    return true;
            
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage());
        }
	}

	/**
	 * Load data for named module.
	 *
	 * @param string $ModuleName
	 *
	 * @return bool
	 */
	public function loadModuleData()
	{
		if ( empty($this->ESMODULE_NAME) )
		{
			throw new Exception('Error loading module-data. No module-name set.');
		}

        try {
            $pdo = RsPDO::getInstance();
            $sql = $pdo -> formatQuery('SELECT `ESMODULE_ID`, `ESMODULE_DESC` FROM `ESMODULE` WHERE `ESMODULE_NAME` = :modulename');
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':modulename', $this->ESMODULE_NAME);
            $stmt -> execute();
            $result = $stmt -> fetchObject();
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }

		$this->ESMODULE_ID   = $result -> ESMODULE_ID;
		$this->ESMODULE_DESC = $result -> ESMODULE_DESC;

		return true;
	}

	/**
	 *
	 * @param string $ESMODULE_NAME
	 *
	 * @param bool
	 */
	final public function setName($ESMODULE_NAME)
	{
		$this->ESMODULE_NAME = $ESMODULE_NAME;
		return $this->loadModuleData();
	}

	/**
	 *
	 */
	final public function setModuleByMimetype($p_mimetype)
	{
		// use only MIME's "type/subtype" specification, skip optional parameters
		$MimeTypeParts = explode(';', $p_mimetype);
		$MimeType = $MimeTypeParts[0];

        try {
            $pdo = RsPDO::getInstance();
            $sql = $pdo -> formatQuery('SELECT `ESMODULE_NAME` FROM `ESMODULE`, `REL_ESMODULE_MIMETYPE` WHERE `ESMODULE_ID` = `REL_ESMODULE_MIMETYPE_ESMODULE_ID` AND `REL_ESMODULE_MIMETYPE_TYPE` = :mimetype');
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':mimetype', $MimeType);
            $stmt -> execute();
            $result = $stmt -> fetchObject();
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }

		if ( empty($result -> ESMODULE_NAME) ) {
		    return false;
            //just use default module (and download)
			//throw new Exception('Cannot load module having empty module-name.');
		}

		$this->ESMODULE_NAME = $result -> ESMODULE_NAME;

		return true;
	}

	/**
	 *
	 * @param string $p_RESOURCE_TYPE
	 * @param string $p_RESOURCE_VERSION
	 *
	 * @return bool
	 */
	final public function setModuleByResource($p_RESOURCE_TYPE, $p_RESOURCE_VERSION) {
		switch (true) {

            case ($p_RESOURCE_TYPE == 'h5p') :
                $this->ESMODULE_NAME = 'h5p';
                break;

            case ($p_RESOURCE_TYPE == 'edutool-vanilla') :
                $this->ESMODULE_NAME = 'lti';
                break;
                
            case ($p_RESOURCE_TYPE == 'edutool-etherpad') :
                $this->ESMODULE_NAME = 'lti';
                break;
            
			case ($p_RESOURCE_TYPE == 'ADL SCORM' && $p_RESOURCE_VERSION == '1.2') :
				$this->ESMODULE_NAME = 'scorm12';
				break;

			case ($p_RESOURCE_TYPE == 'imsqti' && $p_RESOURCE_VERSION == 'xmlv2p1') :
				if (file_exists(dirname(__FILE__).'/../../modules/qti21/config.php')) {
					$this->ESMODULE_NAME = 'qti21';
				} else {
					error_log('Module qti21 not configured so use default ("doc") module.');
					$this->ESMODULE_NAME = 'doc';
				}
				break;

			case ($p_RESOURCE_TYPE == 'moodle') :
				$this->ESMODULE_NAME = 'moodle';
				break;
				
			case ($p_RESOURCE_TYPE == 'Edu-Sharing Scenario' && substr($p_RESOURCE_VERSION, 0, 1) == '1') :
				$this->ESMODULE_NAME = 'scenario';
				break;

			case ($p_RESOURCE_TYPE == 'eduhtml' && substr($p_RESOURCE_VERSION,0,1) == '1') :
				$this->ESMODULE_NAME = 'html';
				break;

			default :
				error_log('Could not set module by resource-type/-version.');
				return false;
		}

		error_log('Using module "'.$this->ESMODULE_NAME.'" from resource-type/-version.');

		return true;
	}


	/**
	 *
	 */
	final public function setCache()
	{
		return true;
	}


	/**
	 *
	 */
	final public function getName()
	{
		return $this->ESMODULE_NAME;
	}

	/**
	 *
	 */
	final public function getModuleId()
	{	
		if(empty($this->ESMODULE_ID))
			$this -> setModuleIdByModuleName();		
		return $this->ESMODULE_ID;
	}
	
	/**
	 *
	 */
	public function setModuleIdByModuleName() {	
		try {
			$pdo = RsPDO::getInstance();
			$sql = $pdo -> formatQuery('SELECT `ESMODULE_ID` FROM `ESMODULE` WHERE `ESMODULE_NAME` = :name');
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindValue(':name', $this -> ESMODULE_NAME);
			$stmt -> execute();
			$result = $stmt -> fetchObject();
			$this -> ESMODULE_ID = $result -> ESMODULE_ID;
		} catch(PDOException $e) {
			throw new Exception($e -> getMessage());
		}
	}

	/**
	 *
	 */
	final public function getTmpFilepath()
	{
		return 'modules/cache/'.$this -> getName();
	}

	/**
	 *
	 */
	final public function getConf($p_param, $p_default = null)
	{
		if (empty($this->ESMODULE_CONF))
		{
			return $p_default;
		}

		if (empty($this->parsed_conf))
		{
			$this->parsed_conf = simplexml_load_string($this->ESMODULE_CONF);
		}

		if (property_exists($this->parsed_conf, $p_param))
		{
			return $this->parsed_conf->{$p_param};
		}

		return $p_default;
	}



}

