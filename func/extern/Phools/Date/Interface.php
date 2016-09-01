<?php

/**
 *
 *
 */
interface Phools_Date_Interface
{

	/**
	 *
	 * @param int $Year
	 * @return Phools_Date_Interface
	 */
	public function setYear($Year);

	/**
	 *
	 * @param int $Month
	 * @return Phools_Date_Interface
	 */
	public function setMonth($Month);

	/**
	 *
	 * @param int $Day
	 * @return Phools_Date_Interface
	 */
	public function setDay($Day);

	/**
	 *
	 * @param int $Hour
	 * @return Phools_Date_Interface
	 */
	public function setHour($Hour);

	/**
	 *
	 * @param int $Minute
	 * @return Phools_Date_Interface
	 */
	public function setMinute($Minute);

	/**
	 *
	 * @param int $Second
	 * @return Phools_Date_Interface
	 */
	public function setSecond($Second);

	/**
	 *
	 * @param int $Microsecond
	 * @return Phools_Date_Interface
	 */
	public function setMicrosecond($Microsecond);

}
