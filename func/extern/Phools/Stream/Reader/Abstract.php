<?php

/**
 *
 *
 *
 */
abstract class Phools_Stream_Reader_Abstract
implements Phools_Stream_Reader_Interface
{

	/**
	 *
	 * @param Phools_Stream_Input_Interface $InputStream
	 */
	public function __construct(Phools_Stream_Input_Interface $InputStream)
	{
		$this->setInputStream($InputStream);
	}

	/**
	 *
	 *
	 */
	public function __destruct()
	{
		$this->InputStream = null;
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Input_Interface
	 */
	protected $InputStream = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Input_Interface $InputStream
	 * @return Phools_Stream_Reader_Abstract
	 */
	public function setInputStream(Phools_Stream_Input_Interface $InputStream)
	{
		$this->InputStream = $InputStream;
		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Input_Interface
	 */
	protected function getInputStream()
	{
		return $this->InputStream;
	}

}
