<?php

/**
 *
 *
 */
interface Phools_Stream_Writer_Interface
{

	/**
	 *
	 * @param Phools_Stream_Character_Interface $Character
	 */
	public function write(Phools_Stream_Character_Interface $Character);

}
