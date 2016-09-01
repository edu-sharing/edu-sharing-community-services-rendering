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
 * Class provides unstructured header-fields, containing unstructured textual
 * information.
 *
 * An example would be the Subject-header in its form "Subject: Example".
 *
 *
 */
class Phools_Net_Smtp_Header_Unstructured
extends Phools_Net_Smtp_Header_Abstract
{

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 */
	public function __construct($Name, $Value = '')
	{
		parent::__construct($Name);

		$this->setValue($Value);
	}

	public function write(Phools_Net_Smtp_Writer_Interface $Formatter)
	{
		return $Formatter->writeHeaderUnstructured($this->getName(), $this->getValue());
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Value = '';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Header_Interface::setValue()
	 */
	public function setValue($Value)
	{
		$this->Value = (string) $Value;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Header_Interface::getValue()
	 */
	public function getValue()
	{
		return $this->Value;
	}

}
