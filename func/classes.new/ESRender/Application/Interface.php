<?php

/**
 *
 *
 *
 */
interface ESRender_Application_Interface
{

	/**
	 *
	 * @var string
	 */
	const DISPLAY_MODE_DOWNLOAD = 'download';
	const DISPLAY_MODE_INLINE = 'inline';
    const DISPLAY_MODE_LOCKED = 'locked';
    const DISPLAY_MODE_DYNAMIC = 'dynamic';
    const DISPLAY_MODE_EMBED = 'embed';

	const DEFAULT_WIDTH = 320;
	const DEFAULT_HEIGHT = 240;

	/**
	 *
	 * @return string
	 */
	public function getDefaultDisplayMode();

	/**
	 *
	 * @return int
	 */
	public function getDefaultWidth();

	/**
	 *
	 * @return int
	 */
	public function getDefaultHeight();

	/**
	 * Track object-usage, a.k.a. who rendered which object in which version
	 * coming from which application ...
	 *
	 * @param string $RepositoryId
	 * @param string $ApplicationId
	 * @param string $ObjectId
	 * @param string $ObjectName
	 * @param string $ObjectVersion
	 * @param string $ModuleId
	 * @param string $ModuleName
	 * @param string $UserId
	 * @param string $UserName
	 * @param string $CourseId
	 *
	 */
	public function trackObject($RepositoryId, $ApplicationId, $esObjectId, $ObjectId,
		$ObjectName, $ObjectVersion, $ModuleId, $ModuleName, $UserId,
		$UserName, $CourseId = null);

}
