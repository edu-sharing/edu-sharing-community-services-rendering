<?php

/**
 *
 *
 *
 */
interface Phools_Net_Connection_Interface
{

	/**
	 * Opens this connection. Return false in case of error.
	 *
	 * @return Phools_Net_Connection_Interface
	 */
	public function open();

	/**
	 * Returns true if this connection is opened, false if closed.
	 *
	 * @return bool
	 */
	public function isEstablished();

	/**
	 * Closes this connection. This operation shall always succeed.
	 *
	 * @return Phools_Net_Connection_Interface
	 */
	public function close();

	/**
	 *
	 * @return bool
	 */
	public function eof();

	/**
	 * Read some bytes up to $Length.
	 *
	 * @param int $Length
	 *
	 * @throws Phools_Net_Connection_Exception
	 *
	 * @return string
	 */
	public function read($Length = 1);

	/**
	 * Transmit given bytes.
	 *
	 * @param string $Data
	 *
	 * @throws Phools_Net_Connection_Exception
	 *
	 * @return int
	 */
	public function write($Data);

	/**
	 *
	 * @return bool
	 */
	public function startTls();
}

