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
 * mc_FileFactory
 *
 * @author [Autor]
 */
class mc_File
{
	public static function factory ($p_filename = null, $p_type = null)
	{

		if (empty($p_filename))
		{
			$p_filename = null;
		}
		else if (empty($p_type))
		{
			$p_type = strtolower(pathinfo($p_filename, PATHINFO_EXTENSION));
		}

		switch($p_type)
		{
			case 'zip' :
				include_once(MC_LIB_PATH.'File/File_Zip.class.php');
				return new mc_File_Zip($p_filename);

			default :
				include_once(MC_LIB_PATH.'File/File_Base.class.php');
				return new mc_File_Base($p_filename);

		}

		return true;
	} // end factory



	public static function info($p_filename)
	{
		$info = pathinfo($p_filename);

		return (array(
			'basename'  => (empty($info['basename'])  ? '' : $info['basename']),
			'dirname'   => (empty($info['dirname'])   ? '' : $info['dirname']),
			'extension' => (empty($info['extension']) ? '' : $info['extension']),
		));
	} // end method info



} // end class mc_File

?>