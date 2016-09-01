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
 *
 */
interface Phools_Request_Http_Interface
extends Phools_Request_Interface
{

	/**
	 * Return the HTTP-version for this request.
	 *
	 * @throws Exception
	 *
	 * @return string
	 */
	public function getHttpVersion();

	/**
	 * Return the request-method used.
	 *
	 * @throws Exception
	 *
	 * @return string
	 */
	public function getRequestMethod();

	/**
	 * Get query-param $Name, optionally apply given $Filter.
	 *
	 * @param string $Name
	 * @param Phools_Filter_Interface $Filter
	 *
	 * @return mixed
	 */
	public function getQueryParam($Name, Phools_Filter_Interface $Filter = null);

	/**
	 * Get post-param $Name, optionally apply given $Filter.
	 *
	 * @param string $Name
	 * @param Phools_Filter_Interface $Filter
	 *
	 * @return mixed
	 */
	public function getPostParam($Name, Phools_Filter_Interface $Filter = null);

	/**
	 * Get cookie $Name, optionally apply given $Filter.
	 *
	 * @param string $Name
	 * @param Phools_Filter_Interface $Filter
	 *
	 * @return mixed
	 */
	public function getCookie($Name, Phools_Filter_Interface $Filter = null);

	/**
	 * Authentication
	 *
	 * @return string
	 */
	public function getAuthMethod();

	/**
	 * Authentication
	 *
	 * @return string
	 */
	public function getAuthDigest();

	/**
	 * Authentication
	 *
	 * @return string
	 */
	public function getAuthUser();

	/**
	 * Authentication
	 *
	 * @return string
	 */
	public function getAuthPassword();

	/**
	 *
	 * @return string
	 */
	public function getRemoteAddr();

}
