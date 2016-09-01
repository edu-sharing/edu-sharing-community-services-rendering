<?php

/**
 *
 *
 */
abstract class Phools_Escaping_Abstract
implements Phools_Escaping_Interface
{

	/**
	 *
	 * @param string $Charset
	 */
	public function __construct($Charset = 'UTF-8')
	{
		$this->setCharset($Charset);
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Charset = 'UTF-8';

	/**
	 *
	 *
	 * @param string $Charset
	 * @return Phools_Escaping_Abstract
	 */
	public function setCharset($Charset)
	{
		$this->Charset = (string) $Charset;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getCharset()
	{
		return $this->Charset;
	}

}
