<?php

ESRender_Autoload::addDirectory(__DIR__ .'/..');

/**
 * Provide auto-loading capabilities for the "ESRender"-project. Include this
 * file using require_once(), then register the autoload-method by calling
 * spl_autoload_register(array('ESRender_Autoload', 'autoload')).
 *
 *
 */
abstract class ESRender_Autoload
{

	/**
	 *
	 * @var array
	 */
	public static $Directories = array();

	/**
	 * Add directory to include when autoloading.
	 *
	 * @param string $Directory
	 *
	 * @throws Exception
	 */
	public static function addDirectory($Directory)
	{
		if ( !file_exists($Directory) )
		{
			throw new Exception('Directory not found.');
		}

		if ( ! in_array($Directory, self::$Directories) )
		{
			self::$Directories[] = $Directory;
		}
	}

	/**
	 * Register this class with php's SPL-autoloading.
	 *
	 * @throws Exception
	 */
	public static function register()
	{
		if ( ! spl_autoload_register(array('ESRender_Autoload', 'autoload')) )
		{
			throw new Exception('SPL autoload functionality not available.');
		}
	}

	/**
	 *
	 *
	 * @param string $Classname
	 *
	 * @return bool
	 */
	public static function autoload($Classname)
	{
		$ClassFilename = implode( DIRECTORY_SEPARATOR, explode('_', $Classname) );
		$ClassFilename .= '.php';

		foreach( self::$Directories as $Directory )
		{
			$Filename = $Directory . DIRECTORY_SEPARATOR . $ClassFilename;
			$Filename = realpath($Filename);

			if ( file_exists($Filename) )
			{
				return include($Filename);
			}
		}

		return false;
	}

}
