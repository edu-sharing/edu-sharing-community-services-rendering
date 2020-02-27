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
    const DISPLAY_MODE_PRERENDER = 'prerender';

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
	 * Track object-usage
     *
	 * @param string $ObjectId
	 *
	 */
	public function trackObject($ObjectId);

}
