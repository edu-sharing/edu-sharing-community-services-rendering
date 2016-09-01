<?php

/*
 * This product Copyright 2010 metaVentis GmbH.  For detailed notice,
 * see the "NOTICE" file with this distribution.
 * 
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

/**
 *
 */
abstract class Phools_Response_Abstract
implements Phools_Response_Interface
{
	
	/**
	 * 
	 * 
	 * @var string
	 */
	protected $Content = '';
	
	/**
	 * (non-PHPdoc)
	 * @see Phools_Response_Interface::appendContent()
	 */
	public function appendContent($Content)
	{
		$this->Content .= (string) $Content;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Phools_Response_Interface::setContent()
	 */
	public function setContent($Content)
	{
		$this->Content = (string) $Content;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Phools_Response_Interface::clearContent()
	 */
	public function clearContent()
	{
		$this->Content = '';
		return $this;
	}
	
	/**
	 *
	 *
	 * @var 
	 */
	protected $Status = self::RESPONSE_STATUS_OK;
	
	/**
	 *
	 *
	 * @param  $Status
	 * @return Phools_Response_Abstract
	 */
	public function setStatus($Status)
	{
		$this->Status = $Status;
		return $this;
	}
	
	/**
	 *
	 * @return 
	 */
	protected function getStatus()
	{
		return $this->Status;
	}
	
	/**
	 *
	 *
	 * @var 
	 */
	protected $ContentType = 'text/html';
	
	/**
	 *
	 *
	 * @param  $ContentType
	 * @return Phools_Response_Abstract
	 */
	public function setContentType($ContentType)
	{
		$this->ContentType = (string) $ContentType;
		return $this;
	}
	
	/**
	 *
	 * @return 
	 */
	protected function getContentType()
	{
		return $this->ContentType;
	}
	
	/**
	 *
	 *
	 * @var string
	 */
	protected $Charset = 'utf-8';
	
	/**
	 *
	 *
	 * @param string $Charset
	 * @return Phools_Response_Abstract
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
	
	/**
	 *
	 *
	 * @var int
	 */
	protected $ContentLength = 0;
	
	/**
	 *
	 *
	 * @param int $ContentLength
	 * @return Phools_Response_Http
	 */
	public function setContentLength($ContentLength)
	{
		$this->ContentLength = (int) $ContentLength;
		return $this;
	}
	
	/**
	 *
	 * @return int
	 */
	protected function getContentLength()
	{
		return $this->ContentLength;
	}
	
	/**
	 *
	 *
	 * @var string
	 */
	protected $Location = '';
	
	/**
	 *
	 *
	 * @param string $Location
	 * @return Phools_Response_Abstract
	 */
	public function setLocation($Location)
	{
		$this->Location = (string) $Location;
		return $this;
	}
	
	/**
	 *
	 * @return string
	 */
	protected function getLocation()
	{
		return $this->Location;
	}
	
}

