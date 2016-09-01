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
abstract class Phools_Net_Smtp_Header_Abstract
implements Phools_Net_Smtp_Header_Interface
{

	/**
	 *
	 * @param string $Name
	 */
	public function __construct($Name)
	{
		$this->setName($Name);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Name = null;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Name = '';

	/**
	 *
	 *
	 * @param string $Name
	 * @return Phools_Net_Smtp_Header
	 */
	protected function setName($Name)
	{
		$this->Name = (string) $Name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->Name;
	}

}
