<?php

/**
 *
 *
 *
 */
class Phools_Hashing_Md5
implements Phools_Hashing_Interface
{

	public function hash($String, $Salt = '')
	{
		$String .= $Salt;
		return md5($String);
	}

}
