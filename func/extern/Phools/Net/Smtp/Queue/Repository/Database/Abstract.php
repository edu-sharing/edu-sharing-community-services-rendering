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
abstract class Metacoon_Net_Smtp_Queue_Repository_Database_Abstract
implements Metacoon_Net_Smtp_Queue_Repository_Interface
{

	/**
	 * Hold the table-prefix to be prepended to table-name.
	 *
	 * @var string
	 */
	protected $TablePrefix = '';

	/**
	 * Set the table-prefix to be prepended to actual table-name.
	 *
	 * @param string $TablePrefix
	 * @return Metacoon_Net_Smtp_Transport_Queue_DBMC
	 */
	public function setTablePrefix($TablePrefix)
	{
		$this->TablePrefix = (string) $TablePrefix;
		return $this;
	}

	/**
	 * Hold the table-name where queue-items are stored.
	 *
	 * @var string
	 */
	protected $TableName = 'mail_queue';

	/**
	 * Set the table-name to store queue-items.
	 *
	 * @param string $TableName
	 * @return Metacoon_Net_Smtp_Transport_Queue_DBMC
	 */
	public function setTableName($TableName)
	{
		$this->TableName = (string) $TableName;
		return $this;
	}

	/**
	 * Construct fully qualified table-name to use in SQL-statements.
	 *
	 * @return string
	 */
	protected function getTableName()
	{
		$TableName = $this->TablePrefix;
		$TableName .= $this->TableName;

		return $TableName;
	}

}
