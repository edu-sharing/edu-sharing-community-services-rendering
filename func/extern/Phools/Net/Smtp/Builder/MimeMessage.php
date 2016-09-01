<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Builder_MimeMessage
extends Phools_Net_Smtp_Builder_SimpleMessage
{

	/**
	 * Start building a new MIME-message "multipart/mixed".
	 *
	 * @param string $Address
	 * @param string $Name
	 * @param string $MimeType
	 */
	public function newMessage($Address, $Name = '', $MimeType = 'multipart/mixed')
	{
		$Author = new Phools_Net_Smtp_Address_Mailbox($Address, $Name);
		$ContentType = new Phools_Net_Mime_Type($MimeType);
		$MimeVersion = Phools_Net_Mime_Version::MIME_VERSION_1_0;

		$Message = new Phools_Net_Smtp_Message_Mime(
			$Author,
			$ContentType,
			$MimeVersion);

		$this->setMessage($Message);

		return $this;
	}

	/**
	 *
	 *
	 * @param string $ContentId
	 *
	 * @return Phools_Net_Smtp_Builder_Mime
	 */
	public function contentId($ContentId)
	{
		$this->getMessage()->setContentId($ContentId);

		return $this;
	}

	/**
	 *
	 *
	 * @param string $ContentDescription
	 *
	 * @return Phools_Net_Smtp_Builder_Mime
	 */
	public function contentDescription($ContentDescription)
	{
		$this->getMessage()->setContentDescription($ContentDescription);

		return $this;
	}

	/**
	 *
	 * @param string $Filename
	 * @param string $MimeType
	 */
	public function attachText(
		$Text,
		$MimeType = 'text/plain',
		$DispositionType = Phools_Net_Mime_ContentDisposition::ATTACHMENT)
	{
		$ContentType = new Phools_Net_Mime_Type($MimeType);
		$MimePart = new Phools_Net_Mime_Part_String($Text, $ContentType);

		$TransferEncoding = new Phools_Net_Mime_Encoding_Base64();
		$MimePart->setTransferEncoding($TransferEncoding);

		$ContentDisposition = new Phools_Net_Mime_ContentDisposition($DispositionType);
		$MimePart->setContentDisposition($ContentDisposition);

		$this->getMessage()->appendMimePart($MimePart);

		return $this;
	}

	/**
	 *
	 * @param string $File
	 * @param string $MimeType
	 */
	public function attachFile(
		$Filename,
		$MimeType = 'application/octet-stream',
		$ContentDisposition = Phools_Net_Mime_ContentDisposition::ATTACHMENT)
	{
		$ContentType = new Phools_Net_Mime_Type($MimeType);
		$MimePart = new Phools_Net_Mime_Part_File($Filename, $ContentType);

		$TransferEncoding = new Phools_Net_Mime_Encoding_Base64();
		$MimePart->setTransferEncoding($TransferEncoding);

		if ( ! file_exists($Filename) )
		{
			throw new Phools_Net_Mime_Exception('File "'.$Filename.'" not found.');
		}

		if ( is_dir($Filename) )
		{
			throw new Phools_Net_Mime_Exception('File "'.$Filename.'" not a file.');
		}

		if ( ! is_readable($Filename) )
		{
			throw new Phools_Net_Mime_Exception('File "'.$Filename.'" not readable.');
		}

		$ContentDisposition = new Phools_Net_Mime_ContentDisposition($ContentDisposition);

		$ContentFilename = basename($Filename);
		$ContentDisposition->setParam('filename', $ContentFilename);

		$Filesize = filesize($Filename);
		$ContentDisposition->setParam('size', $Filesize);

		$MimePart->setContentDisposition($ContentDisposition);

		$this->getMessage()->appendMimePart($MimePart);

		return $this;
	}

}
