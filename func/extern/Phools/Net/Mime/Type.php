<?php

/**
 *
 *
 *
 */
class Phools_Net_Mime_Type
implements Phools_Net_Mime_Type_Interface
{

	/**
	 *
	 * @param string $MimeType
	 * @param string $Subtype
	 * @param array $Params
	 */
	public function __construct($MimeType = 'text/plain', array $Params = array())
	{
		$this->setMimeType($MimeType);

		if ( 0 < count($Params) )
		{
			foreach( $Params as $Name => $Value)
			{
				$this->setParam($Name, $Value);
			}
		}
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Params = null;
		$this->MimeType = null;
	}

	/**
	 *
	 *
	 * @var
	 */
	protected $MimeType = '';

	/**
	 *
	 * @param string $MimeType
	 * @return Phools_Net_Mime_Type
	 */
	protected function setMimeType($MimeType)
	{
		$this->MimeType = (string) $MimeType;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Type_Interface::getMimeType()
	 */
	public function getMimeType()
	{
		return $this->MimeType;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Type_Interface::getPrimaryType()
	 */
	public function getPrimaryType()
	{
		$Parts = explode('/', $this->getMimeType());
		if ( ! $Parts[0] )
		{
			throw new Phools_Net_Mime_Exception('Error extracting type.');
		}

		return $Parts[0];
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Type_Interface::getSubtype()
	 */
	public function getSubtype()
	{
		$Parts = explode('/', $this->getMimeType());
		if ( ! $Parts[1] )
		{
			throw new Phools_Net_Mime_Exception('Error extracting subtype.');
		}

		return $Parts[1];
	}

	/**
	 *
	 * @var array
	 */
	private $Params = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Type_Interface::setParam()
	 */
	public function setParam($Name, $Value = '')
	{
		$this->Params[(string) $Name] = (string) $Value;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Type_Interface::getParam()
	 */
	public function getParam($Name)
	{
		if ( isset($this->Params[$Name]) )
		{
			return $this->Params[$Name];
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_Type_Interface::getParams()
	 */
	public function getParams()
	{
		return $this->Params;
	}

}
