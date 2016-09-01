<?php

/**
 *
 *
 *
 */
interface Phools_Net_Mime_Part_Interface
{

	/**
	 *
	 * @param Phools_Net_Mime_Writer_Interface $Writer
	 *
	 * @return Phools_Net_Mime_Writer_Interface
	 */
	public function write(Phools_Net_Mime_Writer_Interface $Writer);

	/**
	 *
	 * @param Phools_Net_Mime_Type_Interface $Type
	 *
	 * @return Phools_Net_Mime_Part_Interface
	 */
	public function setContentType(Phools_Net_Mime_Type_Interface $Type);

	/**
	 *
	 * @param string $Boundary
	 */
	public function setBoundary($Boundary);

	/**
	 *
	 * @param Phools_Net_Mime_Encoding_Interface $Encoding
	 */
	public function setTransferEncoding(Phools_Net_Mime_Encoding_Interface $Encoding);

	/**
	 *
	 * @param Phools_Net_Mime_ContentDisposition_Interface $ContentDisposition
	 */
	public function setContentDisposition(Phools_Net_Mime_ContentDisposition_Interface $ContentDisposition);

	/**
	 *
	 * @param string $ContentDescription
	 */
	public function setContentDescription($ContentDescription);

	/**
	 *
	 * @param Phools_Net_Mime_Part_Interface $Part
	 *
	 * @return Phools_Net_Mime_Part_Interface
	 */
	public function prependSubpart(Phools_Net_Mime_Part_Interface $MimePart);

	/**
	 *
	 * @param Phools_Net_Mime_Part_Interface $Part
	 *
	 * @return Phools_Net_Mime_Part_Interface
	 */
	public function appendSubpart(Phools_Net_Mime_Part_Interface $MimePart);

}
