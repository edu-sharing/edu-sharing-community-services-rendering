<?php

/**
 *
 *
 *
 */
class Phools_Net_Mime_Part_File
extends Phools_Net_Mime_Part_Abstract
{

	/**
	 *
	 * @param Phools_Net_Mime_Type_Interface $ContentType
	 * @param string $File
	 * @param string $Boundary
	 */
	public function __construct(
		$File,
		Phools_Net_Mime_Type_Interface $ContentType)
	{
		parent::__construct($ContentType);

		$this->setFile($File);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Part_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->File = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Part_Interface::write()
	 */
	public function write(Phools_Net_Mime_Writer_Interface $Writer)
	{
		$ContentType = $this->getContentType();
		$ContentType->setParam('boundary', $this->getBoundary());
		$Writer->writeMimeType(
			$ContentType->getMimeType(),
			$ContentType->getParams());

		$ContentDisposition = $this->getContentDisposition();
		if ( $ContentDisposition )
		{
			$Writer->writeMimeContentDisposition(
				$ContentDisposition->getType(),
				$ContentDisposition->getParams());
		}

		$Content = file_get_contents($this->getFile());
		if ( false === $Content )
		{
			throw new Phools_Net_Smtp_Exception('Error reading file-mime-part.');
		}

		$TransferEncoding = $this->getTransferEncoding();
		if ( $TransferEncoding )
		{
			$Writer->writeMimeTransferEncoding($TransferEncoding->getName());

			$Content = $TransferEncoding->encode($Content);
		}

		$Writer->writeText($Content);

		$MimeParts = $this->getSubparts();
		if ( 0 < count($MimeParts) )
		{
			while( $MimePart = array_shift($MimeParts) )
			{
				$Writer->writeMimeBoundaryStart($this->getBoundary());

				$MimePart->write($Writer);

				$Writer->writeMimeBoundaryEnd($this->getBoundary());
			}
		}

		return $this;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $File = '';

	/**
	 *
	 *
	 * @param string $File
	 * @return Phools_Net_Mime_Part_File
	 */
	public function setFile($File)
	{
		$this->File = (string) $File;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getFile()
	{
		return $this->File;
	}

}
