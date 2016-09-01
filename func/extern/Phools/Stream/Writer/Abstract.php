<?php

/**
 *
 *
 *
 */
abstract class Phools_Stream_Writer_Abstract
implements Phools_Stream_Writer_Interface
{

	/**
	 *
	 * @param Phools_Stream_Output_Interface $OutputStream
	 */
	public function __construct(Phools_Stream_Output_Interface $OutputStream)
	{
		$this->setOutputStream($OutputStream);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->OutputStream = null;
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Output_Interface
	 */
	protected $OutputStream = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Output_Interface $OutputStream
	 * @return Phools_Stream_Writer_Abstract
	 */
	public function setOutputStream(Phools_Stream_Output_Interface $OutputStream)
	{
		$this->OutputStream = $OutputStream;
		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Output_Interface
	 */
	protected function getOutputStream()
	{
		return $this->OutputStream;
	}

}
