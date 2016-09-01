<?php

/**
 *
 *
 *
 */
interface ESRender_LicenseFactory_Interface
{

	/**
	 *
	 * @param string $Name
	 *
	 * @return ESRender_License_Interface
	 */
	public function getLicense($Name, $Author, $permaLink, $fileName);

}
