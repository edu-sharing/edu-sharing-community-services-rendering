<?php

/**
 *
 *
 *
 */
interface Phools_Net_Mime_Writer_Interface
{

	/**
	 *
	 *
	 */
	public function writeMimeVersion($Version);

	/**
	 *
	 * @param string $Boundary
	 *
	 * @return Phools_Net_Mime_Writer_Interface
	 */
	public function writeMimeBoundaryStart($Boundary);

	/**
	 *
	 * @param string $Boundary
	 *
	 * @return Phools_Net_Mime_Writer_Interface
	 */
	public function writeMimeBoundaryEnd($Boundary);

	/**
	 *
	 *
	 * @return Phools_Net_Mime_Writer_Interface
	 */
	public function writeMimeType($MimeType, array $Params = array());

	/**
	 *
	 *
	 * @return Phools_Net_Mime_Writer_Interface
	 */
	public function writeMimeContentDisposition($Type, array $Params = array());

	/**
	 *
	 * @param string $Name
	 *
	 * @return Phools_Net_Mime_Writer_Interface
	 */
	public function writeMimeTransferEncoding($Name);

	/**
	 *
	 * @param string $Content
	 *
	 * @return Phools_Net_Mime_Writer_Interface
	 */
	public function writeMimeContent($Content);

}
