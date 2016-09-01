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


/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 */
class ESUsage extends Plattform
{

	protected $ESUSAGE_ID;
	protected $ESUSAGE_ESAPPLICATION_ID;
	protected $ESUSAGE_ESOBJECT_ID;
	protected $ESUSAGE_COURSEID;
	protected $ESUSAGE_RESSOURCEID;
	protected $ESUSAGE_LMSID;
	protected $ESUSAGE_NODEID;
	protected $ESUSAGE_PARENTNODEID;
	protected $ESUSAGE_APPUSERMAIL;
	protected $ESUSAGE_APPUSER;
	protected $ESUSAGE_FROMUSED;
	protected $ESUSAGE_TOUSED;
	protected $ESUSAGE_DISTINCTPERSONS;
	protected $ESUSAGE_USAGECOUNTER;
	protected $ESUSAGE_USAGEVERSION;
	protected $ESUSAGE_USAGEXMLPARAMS;


	/**
	 *
	 */
	public function __construct()
	{

			parent::__contructor();

			$this->ESUSAGE_ID   = 0 ;
			$this->ESUSAGE_ESAPPLICATION_ID= 0 ;
			$this->ESUSAGE_ESOBJECT_ID= 0 ;
			$this->ESUSAGE_COURSEID= 0 ;
			$this->ESUSAGE_RESSOURCEID= '';
			$this->ESUSAGE_LMSID     = '';
			$this->ESUSAGE_NODEID= '';
			$this->ESUSAGE_PARENTNODEID= '';
			$this->ESUSAGE_APPUSERMAIL= '';
			$this->ESUSAGE_APPUSER = 0;
			$this->ESUSAGE_FROMUSED= '';
			$this->ESUSAGE_TOUSED= '';
			$this->ESUSAGE_DISTINCTPERSONS= 0;
			$this->ESUSAGE_USAGECOUNTER= 0;
			$this->ESUSAGE_USAGEVERSION= '';
			$this->ESUSAGE_USAGEXMLPARAMS= '';

	return true;

	} // end constructor


	/**
	 *
	 */
	final public function getObjectData($p_rep_id,$p_obj_id)
	{
	}

	/**
	 *
	 */
	final public function setCache()
	{
	    return true;
	}


	final public function setObjectData($p_DataArray)
	{
	}

} // end class EsObject

