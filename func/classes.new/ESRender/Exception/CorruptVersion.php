<?php

/**
 * Exception shall be thrown when an object has no/corrupted version. E.g. error on upload, missing origin for collection references.
 *
 *
 */
class ESRender_Exception_CorruptVersion
extends ESRender_Exception_Abstract
{

	/**
	 *
	 * @param string $title
	 */
	public function __construct($title, $Message = '', $Code = '', $Previous = null)
	{
		parent::__construct($Message, $Code, $Previous);
		$this->setTitle($title);
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 *
	 *
	 * @param string $title
	 * @return ESRender_Exception_CorruptVersion
	 */
	protected function setTitle($title)
	{
		$this->title = (string) $title;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

}
