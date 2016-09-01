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
 * Abstract class to enumerate possible response-status.
 * 
 * 
 */
abstract class Phools_Response_Status
{
	
	/**
	 * Shall equal HTTP-status 200 OK.
	 * @var string
	 */
	const OK = 'OK';
	
	/**
	 * Shall equal HTTP-status 201 Created.
	 * @var string
	 */
	const CREATED = 'CREATED';

	/**
	 * Shall equal HTTP-status 301 Bad Request.
	 * @var string
	 */
	const MOVED_PERMANENTLY = 'MOVED_PERMANENTLY';

	/**
	 * Shall equal HTTP-status 302 Found.
	 * @var string
	 */
	const FOUND = 'FOUND';

	/**
	 * Shall equal HTTP-status 303 See Other.
	 * @var string
	 */
	const SEE_OTHER = 'SEE_OTHER';

	/**
	 * Shall equal HTTP-status 400 Bad Request.
	 * @var string
	 */
	const BAD_REQUEST = 'BAD_REQUEST';

	/**
	 * Shall equal HTTP-status 401 Unauthorized.
	 * @var string
	 */
	const UNAUTHORIZED = 'UNAUTHORIZED';
	/**
	 * @var string
	 */
	const FORBIDDEN = 'FORBIDDEN';
	/**
	 * @var string
	 */
	const NOT_FOUND = 'NOT_FOUND';
		
	/**
	 * @var string
	 */
	const INTERNAL_ERROR = 'INTERNAL_ERROR';

	/**
	 * @var string
	 */
	const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';

	/**
	 * @var string
	 */
	const SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';
	
}
