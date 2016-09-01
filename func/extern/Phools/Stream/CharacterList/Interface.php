<?php

/**
 *
 *
 *
 */
interface Phools_Stream_CharacterList_Interface
{

	/**
	 * Append $Character.
	 *
	 * @param Phools_Stream_Character_Interface $Character
	 */
	public function append(Phools_Stream_Character_Interface $Character);

	/**
	 * Prepend $Character.
	 *
	 * @param Phools_Stream_Character_Interface $Character
	 */
	public function prepend(Phools_Stream_Character_Interface $Character);

}
