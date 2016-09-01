<?php

/**
 *
 *
 *
 */
class Phools_Net_Mime_Part_String
extends Phools_Net_Mime_Part_Abstract
{

	/**
	 *
	 * @param string $String
	 * @param Phools_Net_Mime_Type_Interface $ContentType
	 *
	 * @throws Phools_Net_Mime_Exception
	 */
	public function __construct(
		$String,
		Phools_Net_Mime_Type_Interface $ContentType)
	{
		$this
			->setString($String)
			->setContentType($ContentType);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Part_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->String = null;

		parent::__destruct();
	}

	public function write(Phools_Net_Mime_Writer_Interface $Writer)
	{
		$ContentType = $this->getContentType();
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

		$Content = $this->getString();

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
	protected $String = '';

	/**
	 *
	 *
	 * @param string $String
	 * @return Phools_Net_Mime_Part_String
	 */
	public function setString($String)
	{
		$this->String = (string) $String;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getString()
	{
		return $this->String;
	}

}
