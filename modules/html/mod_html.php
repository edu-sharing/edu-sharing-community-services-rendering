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
class mod_html
extends ESRender_Module_ContentNode_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::createInstance()
	 */
	public function createInstance(ESObject $ESObject)
	{
		if ( ! parent::createInstance($ESObject) )
		{
			return false;
		}

		$extraction_path = $this->getCacheFileName();
		$zip_file = $extraction_path.'.zip';

		if ( ! rename($extraction_path, $zip_file) )
		{
			return false;
		}

		if ( ! mkdir($extraction_path, 0777) )
		{
			return false;
		}

        $zip = new ZipArchive;
        $res = $zip->open($zip_file);
        if ($res === TRUE) {
            $zip->extractTo($extraction_path.DIRECTORY_SEPARATOR);
            $zip->close();
            return true;
        }

        return false;
    }


    protected function dynamic(ESObject $ESObject)
    {
        $template_data['url'] = $this->_ESOBJECT->getPath().'/index.html?' . session_name() . '=' . session_id(). '&token=' . Config::get('token');
        if(Config::get('showMetadata'))
            $template_data['metadata'] = $this -> _ESOBJECT -> metadataHandler -> render($this -> getTemplate(), '/metadata/dynamic');
        $template_data['title'] = $this->_ESOBJECT->getTitle();
        $template_data['previewUrl'] = $this->_ESOBJECT->getPreviewUrl();
        echo $this -> getTemplate() -> render('/module/html/dynamic', $template_data);
        return true;
    }

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::download()
	 */
	protected function download(ESObject $ESObject)
	{
	    $Logger = $this->getLogger();
        $Logger->debug('Redirecting to location: "' . $this->_ESOBJECT->getPath().'.zip?"');
		header('HTTP/1.1 303 See other');
		header('Location: '.$this->_ESOBJECT->getPath().'.zip?' . session_name() . '=' . session_id(). '&token=' . Config::get('token'));

		return true;
	}

}
