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
class Phools_Request_Http
extends Phools_Request_Abstract
implements Phools_Request_Http_Interface
{

	/**
	 * Constant used to indicate HTTP version 1.0.
	 *
	 * @var string
	 */
	const HTTP_VERSION_1_0 = '1.0';

	/**
	 * Constant used to indicate HTTP version 1.1.
	 *
	 * @var string
	 */
	const HTTP_VERSION_1_1 = '1.1';

	/**
	 * Constant used to indicate HTTP request-method OPTIONS.
	 *
	 * @var string
	 */
	const REQUEST_METHOD_OPTIONS = 'OPTIONS';

	/**
	 * Constant used to indicate HTTP request-method GET.
	 *
	 * @var string
	 */
	const REQUEST_METHOD_GET = 'GET';

	/**
	 * Constant used to indicate HTTP request-method HEAD.
	 *
	 * @var string
	 */
	const REQUEST_METHOD_HEAD = 'HEAD';

	/**
	 * Constant used to indicate HTTP request-method POST.
	 *
	 * @var string
	 */
	const REQUEST_METHOD_POST = 'POST';

	/**
	 * Constant used to indicate HTTP request-method PUT.
	 *
	 * @var string
	 */
	const REQUEST_METHOD_PUT = 'PUT';

	/**
	 * Constant used to indicate HTTP request-method DELETE.
	 *
	 * @var string
	 */
	const REQUEST_METHOD_DELETE = 'DELETE';

	/**
	 * Constant used to indicate HTTP request-method TRACE.
	 *
	 * @var string
	 */
	const REQUEST_METHOD_TRACE = 'TRACE';

	/**
	 * (non-PHPdoc)
	 * @see func/extern/Phools/Request/Http/Phools_Request_Http_Interface::getHttpVersion()
	 */
	public function getHttpVersion()
	{
		if ( ! isset($_SERVER['SERVER_PROTOCOL']) )
		{
			throw new RuntimeException('$_SERVER["SERVER_PROTOCOL"] not set.');
		}

		switch( $_SERVER['SERVER_PROTOCOL'] )
		{
			case 'HTTP/1.0':
				$HttpVersion = self::HTTP_VERSION_1_0;
				break;
			case 'HTTP/1.1':
				$HttpVersion = self::HTTP_VERSION_1_1;
				break;
			default:
				throw new RuntimeException('Unknown HTTP-version.');
		}

		return $HttpVersion;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Request_Interface::isSecure()
	 */
	public function isSecure()
	{
		if ( isset($_SERVER['HTTPS']) )
		{
			if ( 'off' != $_SERVER['HTTPS'] )
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see func/extern/Phools/Request/Http/Phools_Request_Http_Interface::getRequestMethod()
	 */
	public function getRequestMethod()
	{
		if ( ! isset($_SERVER['REQUEST_METHOD']) )
		{
			throw new RuntimeException('$_SERVER["REQUEST_METHOD"] not set.');
		}

		switch ( $_SERVER['REQUEST_METHOD'] )
		{
			case 'GET':
				$RequestMethod = self::REQUEST_METHOD_GET;
				break;
			case 'POST':
				$RequestMethod = self::REQUEST_METHOD_POST;
				break;
			case 'HEAD':
				$RequestMethod = self::REQUEST_METHOD_HEAD;
				break;
			case 'OPTIONS':
				$RequestMethod = self::REQUEST_METHOD_OPTIONS;
				break;
			case 'POST':
				$RequestMethod = self::REQUEST_METHOD_POST;
				break;
			case 'PUT':
				$RequestMethod = self::REQUEST_METHOD_PUT;
				break;
			case 'TRACE':
				$RequestMethod = self::REQUEST_METHOD_TRACE;
				break;
			default:
				throw new Exception('Unhandled request-method.');
		}

		return $RequestMethod;
	}

	/**
	 * (non-PHPdoc)
	 * @see func/extern/Phools/Request/Phools_Request_Interface::getParam()
	 */
	public function getParam($Name, Phools_Filter_Interface $Filter = null)
	{
		$Value = false;

		$RequestMethod = $this->getRequestMethod();
		switch( $RequestMethod )
		{
			case self::REQUEST_METHOD_GET:
				$Value = $this->getQueryParam($Name, $Filter);
				break;
			case self::REQUEST_METHOD_POST:
				$Value = $this->getPostParam($Name, $Filter);
				break;
			default:
				// skip unhandled request-methods
		}

		return $Value;
	}

	/**
	 * (non-PHPdoc)
	 * @see func/extern/Phools/Request/Http/Phools_Request_Http_Interface::getQueryParam()
	 */
	public function getQueryParam($Name, Phools_Filter_Interface $Filter = null)
	{
		if ( ! isset($_GET[$Name]) )
		{
			return false;
		}

		$Value = $_GET[$Name];
		if ( get_magic_quotes_gpc() )
		{
			$Value = stripslashes($Value);
		}

		if ( $Filter )
		{
			$Value = $Filter->filter($Value);
		}

		return $Value;
	}

	/**
	 * (non-PHPdoc)
	 * @see func/extern/Phools/Request/Http/Phools_Request_Http_Interface::getPostParam()
	 */
	public function getPostParam($Name, Phools_Filter_Interface $Filter = null)
	{
		if ( ! isset($_POST[$Name]) )
		{
			return false;
		}

		$Value = $_POST[$Name];
		if ( get_magic_quotes_gpc() )
		{
			$Value = stripslashes($Value);
		}

		if ( $Filter )
		{
			$Value = $Filter->filter($Value);
		}

		return $Value;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Request_Http_Interface::getCookie()
	 */
	public function getCookie($Name, Phools_Filter_Interface $Filter = null)
	{
		if ( ! isset($_COOKIE[$Name]) )
		{
			return false;
		}

		$Value = $_COOKIE[$Name];
		if ( get_magic_quotes_gpc() )
		{
			$Value = stripslashes($Value);
		}

		if ( $Filter )
		{
			$Value = $Filter->filter($Value);
		}

		return $Value;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Request_Http_Interface::getAuthMethod()
	 */
	public function getAuthMethod()
	{
		if ( isset($_SERVER['AUTH_TYPE']) )
		{
			return $_SERVER['AUTH_TYPE'];
		}

		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Request_Http_Interface::getAuthDigest()
	 */
	public function getAuthDigest()
	{
		if ( isset($_SERVER['PHP_AUTH_DIGEST']) )
		{
			return $_SERVER['PHP_AUTH_DIGEST'];
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Request_Http_Interface::getAuthUser()
	 */
	public function getAuthUser()
	{
		if ( isset($_SERVER['PHP_AUTH_USER']) )
		{
			return $_SERVER['PHP_AUTH_USER'];
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Request_Http_Interface::getAuthPassword()
	 */
	public function getAuthPassword()
	{
		if ( isset($_SERVER['PHP_AUTH_PW']) )
		{
			return $_SERVER['PHP_AUTH_PW'];
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Request_Http_Interface::getRemoteAddr()
	 */
	public function getRemoteAddr()
	{
		if ( isset($_SERVER['REMOTE_ADDR']) )
		{
			return $_SERVER['REMOTE_ADDR'];
		}

		return false;
	}

}
