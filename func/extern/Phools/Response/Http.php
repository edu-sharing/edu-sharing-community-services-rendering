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
 *
 */
class Phools_Response_Http
extends Phools_Response_Abstract
implements Phools_Response_Http_Interface
{
	
	/**
	 * (non-PHPdoc)
	 * @see func/extern/Phools/Response/Phools_Response_Interface::send()
	 */
	public function send()
	{
		$Status = $this->getStatus();
		switch( $Status )
		{
			case Phools_Response_Status::OK:
				header('HTTP/1.0 200 Ok');
				break;
			case Phools_Response_Status::CREATED:
				header('HTTP/1.0 201 Created');
				break;
			case Phools_Response_Status::MOVED_PERMANENTLY:
				header('HTTP/1.0 301 Unauthorized');
				break;
			case Phools_Response_Status::FOUND:
				header('HTTP/1.0 302 Forbidden');
				break;
			case Phools_Response_Status::SEE_OTHER:
				header('HTTP/1.0 303 See Other');
				break;
			case Phools_Response_Status::BAD_REQUEST:
				header('HTTP/1.0 400 Bad Request');
				break;
			case Phools_Response_Status::UNAUTHORIZED:
				header('HTTP/1.0 401 Unauthorized');
				break;
			case Phools_Response_Status::FORBIDDEN:
				header('HTTP/1.0 403 Forbidden');
				break;
			case Phools_Response_Status::NOT_FOUND:
				header('HTTP/1.0 404 Not Found');
				break;
			case Phools_Response_Status::INTERNAL_ERROR:
				header('HTTP/1.0 500 Internal Server Error');
				break;
			case Phools_Response_Status::NOT_IMPLEMENTED:
				header('HTTP/1.0 501 Not Implemented');
				break;
			case Phools_Response_Status::SERVICE_UNAVAILABLE:
				header('HTTP/1.0 503 Service Unavailable');
				break;
			default:
				throw new Exception('Unhandled response-status.');
		}
		
		$ContentType = $this->getContentType();
		if ( '' != $ContentType )
		{
			$Charset = $this->getCharset();
			if ( '' == $Charset )
			{
				$ContentType .= '; charset='.$Charset;
			}
			
			header('Content-Type: '.$ContentType);
		}
		
		if ( 0 == $this->getContentLength() )
		{
			$ContentLength = strlen($this->Content);
			$this->setContentLength($ContentLength);
			
			header('Content-Length: ' . $this->getContentLength());
		}
		
		$Location = $this->getLocation();
		if ( '' != $Location )
		{
			header('Location: '.$Location);
		}
		
		$Cookies = $this->getCookies();
		while( $Cookie = array_shift($Cookies) )
		{
			$Cookie->set();
		}
		
		echo $this->Content;
		
		return true;
	}
	
	/**
	 *
	 *
	 * @var array
	 */
	protected $Cookies = array();
	
	/**
	 * (non-PHPdoc)
	 * @see func/extern/Phools/Response/Phools_Response_Http_Interface::setCookie()
	 */
	public function setCookie(Phools_Net_Http_Cookie $Cookie)
	{
		$this->Cookies[] = $Cookie;
		return $this;
	}
	
	/**
	 *
	 * @return array
	 */
	public function getCookies()
	{
		return $this->Cookies;
	}
	
}
