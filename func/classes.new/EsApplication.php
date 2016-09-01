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
 * handles the current app
 *
 * @author steffen gross / matthias hupfer
 */
class EsApplication
{
	protected $conf_file;

	public $prop_array;

	/**
	 *
	 */
	public function __construct($p_conf_file)
	{
	    $this->conf_file = $p_conf_file;
	} // end constructor

	/**
	 *
	 */
	final public function readProperties()
	{
		$this->prop_array = array();
		
		if ( ! file_exists($this->conf_file)) {
			throw new Exception('Config file "'.$this->conf_file.'" not found.');
		}

		$l_DOMDocument = new DOMDocument();
		$l_DOMDocument->load($this->conf_file);

		$comment = $l_DOMDocument->getElementsByTagName('comment');
		$list    = $l_DOMDocument->getElementsByTagName('entry');

    $this->prop_array['comment'] = $comment->item(0)->nodeValue;

	foreach ($list as $entry)
		{
	    $this->prop_array[$entry->getAttribute("key")] = $entry->nodeValue;
    }
	}


	final public function saveProperties($_post)
	{
		if (file_exists($this->conf_file)) {
			$l_DOMDocument = new DOMDocument();
			$l_DOMDocument->load($this->conf_file);
		}

		$comment = $l_DOMDocument->getElementsByTagName('comment');
		$list    = $l_DOMDocument->getElementsByTagName('entry');

    $comment->item(0)->nodeValue = $_post['comment'];


	foreach ($list as $entry)
		{
	     $entry->nodeValue = $_post[$entry->getAttribute("key")] ;
    }

	  $l_DOMDocument->save($this->conf_file);

	}

	/**
	 *
	 */
	final public function getForm()
	{
		$form ='<div style="width:800px; position:relative;background-color:white;">';

	foreach ($this->prop_array as $key => $val)
		{
      $label  = constant($key);
	    $form .= '<div><div style="width: 30%;float:left;"><label for="'.$key.'" class="">'.$label.'</label></div>';
	    $form .= '<div style="width: 70%;float:left;"><INPUT type="text" class=""  Value="'.$val.'" id="'.$key.'" name="'.$key.'" size="100"></div><br style="clear:left"></div>';
    }

		$form .='</div>';
   return $form;
	}



	final public function deleteFile($p_filename)
	{
     $li   = $this->getFileList();
     if ($pos = array_search($p_filename,$li)){              
        unset($li[$pos]);     	
     	}
     
     $this->updateList($li);

    		return true;
	}

	final public function updateList($p_filearray)
	{
		$app_str = implode(',',$p_filearray); 

		if (file_exists($this->conf_file)) {
			$l_DOMDocument = new DOMDocument();
			$l_DOMDocument->load($this->conf_file);
		}
		$list = $l_DOMDocument->getElementsByTagName('entry');

	foreach ($list as $entry)
		{
		 if ($entry->getAttribute("key")=="applicationfiles" ){
        $entry->nodeValue = $app_str;
			  break;
		 }
    }

			$l_DOMDocument->save($this->conf_file);
    	return true;
	}


	/**
	 *
	 */
	final public function getFileList()
	{
		if (file_exists($this->conf_file)) {
			$l_DOMDocument = new DOMDocument();
			$l_DOMDocument->load($this->conf_file);
		}
		$list = $l_DOMDocument->getElementsByTagName('entry');

	foreach ($list as $entry)
		{
		 if ($entry->getAttribute("key")=="applicationfiles" ){
			   $app_str  = $entry->nodeValue;
			   break;
		 }
    }
		    
		$app_array = explode(',',$app_str); 
		   
		return $app_array;
	}

	/**
	 * 
	 * @return string
	 */
	public function getType()
	{
		$ApplicationType = $this->prop_array['type'];
		if ( '' == $ApplicationType )
		{
			trigger_error('No type specified for remote-application', E_USER_ERROR);
		}

		return $ApplicationType;
	}

} // end class mc_Session

?>
