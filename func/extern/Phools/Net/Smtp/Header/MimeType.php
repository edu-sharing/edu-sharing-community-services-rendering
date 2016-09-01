<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Header_MimeType
extends Phools_Net_Smtp_Header_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param Phools_Net_Mime_Type_Interface $MimeType
	 */
	public function __construct($Name, Phools_Net_Mime_Type_Interface $MimeType)
	{
		parent::__construct($Name);

		$this->setMimetype($MimeType);
	}

	/**
	 * Free mimetype
	 *
	 */
	public function __destruct()
	{
		$this->Mimetype = null;
	}

	public function write(Phools_Net_Smtp_Writer_Interface $Formatter)
	{
		return $Formatter->writeHeaderMimeType(
			$this->getName(),
			$this->getMimetype());
	}

	/**
	 *
	 * @var Phools_Net_Mime_Type_Interface
	 */
	private $Mimetype = null;

	/**
	 *
	 * @param Phools_Net_Mime_Type_Interface $Mimetype
	 *
	 * @return Phools_Net_Smtp_Header_MimeType
	 */
	public function setMimetype(Phools_Net_Mime_Type_Interface $Mimetype)
	{
		$this->Mimetype = $Mimetype;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Mime_Type_Interface
	 */
	protected function getMimetype()
	{
		return $this->Mimetype;
	}

}
