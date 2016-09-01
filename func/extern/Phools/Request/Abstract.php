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
abstract class Phools_Request_Abstract
implements Phools_Request_Interface
{

	public function __construct($BaseDirectory = '')
	{
		if ( '' == $BaseDirectory )
		{
			$BaseDirectory = dirname($_SERVER['REQUEST_URI']);
		}

		$this->setBaseDirectory($BaseDirectory);
	}

	public function uri($path, array $params = array())
	{
		$uri = $this->getBaseDirectory();
		$uri .= '/' . $path;

		if ( 0 < count($params) )
		{
			foreach($params as $name => $value)
			{
				$uri .= urlencode($name) . '=' . urlencode($value);
			}
		}

		return $uri;
	}

	/**
	 * Convinience-method to allow array-like access to request-parameters.
	 *
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($Offset)
	{
		return $this->paramExists($Offset);
	}

	public function __isset($ParamName)
	{
		return $this->paramExists($ParamName);
	}

	/**
	 * Convinience-method to allow array-like access to request-parameters.
	 *
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($Offset)
	{
		return $this->getParam($Offset);
	}

	public function __get($ParamName)
	{
		return $this->getParam($ParamName);
	}

	/**
	 * As you are not supposed to alter incoming request-data, we'll just throw
	 * an exception to remind you of that.
	 *
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 *
	 * @throws Exception
	 */
	final public function offsetSet($Offset, $Value)
	{
		throw new Exception('It is not allowed to modify request-parameters.');
	}

	/**
	 * As you are not supposed to alter incoming request-data, we'll just throw
	 * an exception to remind you of that.
	 *
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 *
	 * @throws Exception
	 */
	final public function offsetUnset($Offset)
	{
		throw new Exception('It is not allowed to unset request-parameters.');
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $BaseDirectory = '';

	/**
	 *
	 *
	 * @param string $BaseDirectory
	 * @return Phools_Request_Abstract
	 */
	protected function setBaseDirectory($BaseDirectory)
	{
		$this->BaseDirectory = (string) $BaseDirectory;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getBaseDirectory()
	{
		return $this->BaseDirectory;
	}

}
