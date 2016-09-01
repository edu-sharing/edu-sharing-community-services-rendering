<?php

/**
 *
 *
 *
 */
class Phools_Net_Mime_ContentDisposition
implements Phools_Net_Mime_ContentDisposition_Interface
{

	/**
	 *
	 * @var string
	 */
	const INLINE = 'inline';

	/**
	 *
	 * @var string
	 */
	const ATTACHMENT = 'attachment';

	/**
	 *
	 * @param string $Type
	 */
	public function __construct($Type)
	{
		$this->setType($Type);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Params = null;
		$this->Type = null;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Type = '';

	/**
	 *
	 *
	 * @param string $Type
	 * @return Phools_Net_Mime_ContentDisposition
	 */
	protected function setType($Type)
	{
		$this->Type = (string) $Type;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->Type;
	}

	/**
	 *
	 * @var array
	 */
	private $Params = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_ContentDisposition_Interface::setParam()
	 */
	public function setParam($Name, $Value = '')
	{
		$this->Params[(string) $Name] = (string) $Value;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Mime_ContentDisposition_Interface::getParam()
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
	 *
	 * @return array
	 */
	public function getParams()
	{
		return $this->Params;
	}

}
