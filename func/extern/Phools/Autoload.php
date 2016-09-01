<?php

Phools_Autoload::addDirectory(dirname(__FILE__) . '/..');

/**
 * Provide auto-loading capabilities for the "Phools"-project. Include this
 * file using require_once(), then register the autoload-method by calling
 * spl_autoload_register(array('Phools_Autoload', 'autoload')).
 *
 *
 */
abstract class Phools_Autoload
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
			array_push(self::$Directories, $Directory);
		}
	}

	/**
	 * Register this class with php's SPL-autoloading.
	 *
	 * @throws Exception
	 */
	public static function register()
	{
		/*
		 * @see http://php.net/manual/en/function.spl-autoload-functions.php
		 * If the autoload stack is NOT activated then the return value is false
		 */
		if ( ! spl_autoload_register(array('Phools_Autoload', 'autoload')) )
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
		$ClassFilename = str_replace('_', DIRECTORY_SEPARATOR, $Classname);
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
