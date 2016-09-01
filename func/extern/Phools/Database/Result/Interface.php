<?php

interface Phools_Database_Result_Interface
{

	/**
	 *
	 * @return string
	 */
	public function getErrorMessage();

	/**
	 *
	 * @return string
	 */
	public function getErrorCode();

	/**
	 *
	 * @return int
	 */
	public function affectedRows();

	/**
	 *
	 * @return int
	 */
	public function rowCount();

	/**
	 *
	 * @param int $Offset
	 */
	public function fetchField($Offset = 0);

	/**
	 *
	 * @param string $FetchMode
	 * @param string $ClassName
	 * @param array $Args
	 *
	 * @return array
	 */
	public function fetchRow(
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array());

	/**
	 *
	 * @param string $FetchMode
	 * @param string $ClassName
	 * @param array $Args
	 *
	 * @return array
	 */
	public function fetchAll(
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array());

	/**
	 *
	 * @return Phools_Database_Result_Interface
	 */
	public function free();

	/**
	 *
	 * @return bool
	 */
	public function isFree();

}
