<?php

/**
 * Collect possible priority-values.
 *
 *
 */
abstract class Phools_Net_Smtp_Priority
{

	/**
	 *
	 * @var int
	 */
	const UNKNOWN = 0x00;

	/**
	 *
	 * @var int
	 */
	const HIGHEST = 0x01;

	/**
	 *
	 * @var int
	 */
	const HIGH = 0x02;

	/**
	 *
	 * @var int
	 */
	const NORMAL = 0x03;

	/**
	 *
	 * @var int
	 */
	const LOW = 0x04;

	/**
	 *
	 * @var int
	 */
	const LOWEST = 0x05;

}
