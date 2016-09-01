<?php

/**
 *
 *
 *
 */
abstract class Phools_Net_Mime_Part_Abstract
implements Phools_Net_Mime_Part_Interface
{

	/**
	 *
	 * @param Phools_Net_Mime_Type_Interface $ContentType
	 * @param string $Boundary
	 */
	public function __construct(
		Phools_Net_Mime_Type_Interface $ContentType = null)
	{
		$this
			->setContentType($ContentType);
	}

	/**
	 * Free memory.
	 */
	public function __destruct()
	{
		$this->ContentDescription = null;
		$this->ContentId = null;
		$this->ContentDisposition = null;
		$this->Boundary = null;
		$this->TransferEncoding = null;
		$this->ContentType = null;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Mime_Type_Interface
	 */
	protected $ContentType = null;

	/**
	 *
	 *
	 * @param Phools_Net_Mime_Type_Interface $ContentType
	 * @return Phools_Net_Smtp_Message_Mime_Abstract
	 */
	public function setContentType(
		Phools_Net_Mime_Type_Interface $ContentType = null)
	{
		$this->ContentType = $ContentType;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Mime_Type_Interface
	 */
	public function getContentType()
	{
		return $this->ContentType;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Boundary = 'PHOOLS_MIME_PART_BOUNDARY';

	/**
	 *
	 *
	 * @param string $Boundary
	 * @return Phools_Net_Mime_Message
	 */
	public function setBoundary($Boundary)
	{
		$this->Boundary = (string) $Boundary;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getBoundary()
	{
		return $this->Boundary;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Mime_Encoding_Interface
	 */
	protected $TransferEncoding = null;

	/**
	 *
	 *
	 * @param Phools_Net_Mime_Encoding_Interface $TransferEncoding
	 * @return Phools_Net_Mime_Part_Abstract
	 */
	public function setTransferEncoding(Phools_Net_Mime_Encoding_Interface $TransferEncoding)
	{
		$this->TransferEncoding = $TransferEncoding;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Mime_Encoding_Interface
	 */
	protected function getTransferEncoding()
	{
		return $this->TransferEncoding;
	}

	/**
	 *
	 *
	 * @var Phools_Net_Mime_ContentDisposition_Interface
	 */
	protected $ContentDisposition = '';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Part_Interface::setContentDisposition()
	 */
	public function setContentDisposition(
		Phools_Net_Mime_ContentDisposition_Interface $ContentDisposition)
	{
		$this->ContentDisposition = $ContentDisposition;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Mime_ContentDisposition_Interface
	 */
	public function getContentDisposition()
	{
		return $this->ContentDisposition;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $ContentId = '';

	/**
	 *
	 *
	 * @param string $ContentId
	 * @return Phools_Net_Smtp_Message_Mime
	 */
	public function setContentId($ContentId = '')
	{
		$this->ContentId = (string) $ContentId;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getContentId()
	{
		return $this->ContentId;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $ContentDescription = '';

	/**
	 *
	 *
	 * @param string $ContentDescription
	 * @return Phools_Net_Smtp_Message_Mime
	 */
	public function setContentDescription($ContentDescription = '')
	{
		$this->ContentDescription = (string) $ContentDescription;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getContentDescription()
	{
		return $this->ContentDescription;
	}

	/**
	 *
	 * @var array
	 */
	private $Subparts = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Message_Interface::prependSubpart()
	 */
	public function prependSubpart(Phools_Net_Mime_Part_Interface $MimePart)
	{
		array_unshift($this->Subparts, $Part);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Message_Interface::appendSubpart()
	 */
	public function appendSubpart(Phools_Net_Mime_Part_Interface $MimePart)
	{
		array_push($this->Subparts, $Part);

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getSubparts()
	{
		return $this->Subparts;
	}

}
