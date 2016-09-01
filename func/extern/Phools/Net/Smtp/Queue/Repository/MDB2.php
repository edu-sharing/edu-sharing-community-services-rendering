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

CREATE TABLE `dev_notification`.`queue` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`message` TEXT NOT NULL ,
	`sender` TEXT NOT NULL ,
	`recipients` TEXT NOT NULL ,
	`failed_delivery_attempts` INT UNSIGNED NOT NULL ,
	`last_delivery_attempt` INT UNSIGNED NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

 *
 */
class Metacoon_Net_Smtp_Queue_Repository_MDB2
extends Metacoon_Net_Smtp_Queue_Repository_Database_Abstract
{

	/**
	 *
	 * @var string
	 */
	const COLUMN_ITEM_ID = 'id';

	/**
	 *
	 * @var string
	 */
	const COLUMN_MESSAGE = 'message';

	/**
	 *
	 * @var string
	 */
	const COLUMN_SENDER = 'sender';

	/**
	 *
	 * @var string
	 */
	const COLUMN_RECIPIENTS = 'recipients';

	/**
	 *
	 * @var string
	 */
	const COLUMN_FAILED_DELIVERY_ATTEMPTS = 'failed_delivery_attempts';

	/**
	 *
	 * @var string
	 */
	const COLUMN_LAST_DELIVERY_ATTEMPT = 'last_delivery_attempt';

	/**
	 *
	 * @param MDB2_Driver_Common $Database
	 */
	public function __construct(MDB2_Driver_Common $Database)
	{
		$this->setDatabase($Database);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Database = null;
	}

	/**
	 *
	 * @param array $Constraints
	 *
	 * @throws Metacoon_Net_Smtp_Queue_Exception
	 *
	 * @return string
	 */
	protected function buildSqlConstraints(array $Constraints)
	{
		$String = '';

		if ( 0 == count($Constraints) )
		{
			return $String;
		}

		$Database = $this->getDatabase();

		$First = true;
		foreach( $Constraints as $Name => $Value )
		{
			if ( ! $First )
			{
				$String .= ' AND ';
			}

			// testing UPPERCASE here to avoid upper-lower-case-confusion
			switch( strtoupper($Name) )
			{
				case Metacoon_Net_Smtp_Queue_Constraint::ITEM_ID_EQUALS:
					if ( is_array($Value) )
					{
						$String .= $Database->quoteIdentifier(self::COLUMN_ITEM_ID) . ' in (';

						$First = true;
						foreach( $Value as $Id )
						{
							$String .= $First ? $First = false : ', ';
							$String .= $Database->quote($Id, 'INTEGER');
						}

						$String .= ')';
					}
					else
					{
						$String .= $Database->quoteIdentifier(self::COLUMN_ITEM_ID) . ' = ' . $Database->quote($Value, 'INTEGER');
					}
					break;
				default:
					throw new Metacoon_Net_Smtp_Queue_Exception('Unhandled condition "'.$Name.'" found.');
			}

			$First = false;
		}
var_dump($String);

		return $String;
	}

	/**
	 *
	 * @param array $Order
	 *
	 * @throws Metacoon_Net_Smtp_Queue_Exception
	 *
	 * @return string
	 */
	protected function buildSqlOrder(array $Order)
	{
		$String = '';

		if ( 0 == count($Order) )
		{
			return $String;
		}

		$Database = $this->getDatabase();
		if ( ! $Database )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('No database set.');
		}

		$First = true;
		foreach( $Order as $Name )
		{
			if ( ! $First )
			{
				$String .= ', ';
			}

			// testing UPPERCASE here to avoid upper-lower-case-confusion
			switch( strtoupper($Name) )
			{
				case Metacoon_Net_Smtp_Queue_Order::FAILED_DELIVERY_ATTEMPTS_ASC:
					$String .= $Database->quoteIdentifier(self::COLUMN_FAILED_DELIVERY_ATTEMPTS) . ' ASC';
					break;
				default:
					throw new Metacoon_Net_Smtp_Queue_Exception('Unhandled order "'.$Name.'" found.');
			}

			$First = false;
		}

		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Metacoon_Net_Smtp_Queue_Constraint::insert()
	 */
	public function insert(
		$Message,
		$Sender,
		array $Recipients,
		$FailedDeliveryAttempts,
		$LastDeliveryAttempt)
	{
		$Database = $this->getDatabase();
		if ( ! $Database )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('No database set.');
		}

		$Recipients = base64_encode(serialize($Recipients));

		$Sql = 'insert into '
			. $Database->quoteIdentifier($this->getTableName())
		.'('
			. $Database->quoteIdentifier(self::COLUMN_MESSAGE)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_SENDER)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_RECIPIENTS)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_FAILED_DELIVERY_ATTEMPTS)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_LAST_DELIVERY_ATTEMPT)
		.') values ('
			. $Database->quote($Message, 'TEXT')
			. ', ' . $Database->quote($Sender, 'TEXT')
			. ', ' . $Database->quote($Recipients, 'TEXT')
			. ', ' . $Database->quote($FailedDeliveryAttempts, 'INTEGER')
			. ', ' . $Database->quote($LastDeliveryAttempt, 'INTEGER')
		.');';

		$Result = $Database->query($Sql);
		if ( $Database->isError($Result) )
		{
			throw new Metacoon_Net_Smtp_Transport_Exception('Error inserting message into message-repository "'.$Sql.'".');
		}

		$ItemId = $Database->lastInsertId();
		if ( ! $ItemId )
		{
			throw new Metacoon_Net_Smtp_Transport_Exception('Error reading last-insert-id.');
		}

		return $ItemId;
	}

	/**
	 * (non-PHPdoc)
	 * @see Metacoon_Net_Smtp_Queue_Constraint::find()
	 */
	public function findAll(
		array $Constraints = array(),
		array $Order = array(),
		$Limit = 1,
		$Offset = 0)
	{
		$Database = $this->getDatabase();
		if ( ! $Database )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('No database set.');
		}

		$Sql = 'select '
			. $Database->quoteIdentifier(self::COLUMN_ITEM_ID)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_MESSAGE)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_SENDER)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_RECIPIENTS)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_FAILED_DELIVERY_ATTEMPTS)
			. ', ' . $Database->quoteIdentifier(self::COLUMN_LAST_DELIVERY_ATTEMPT)
			.' from '
			. $Database->quoteIdentifier($this->getTableName());

		if ( ! empty($Constraints) )
		{
			$Sql .= ' where ' . $this->buildSqlConstraints($Constraints);
		}

		if ( ! empty($Order) )
		{
			$Sql .= ' order by ' . $this->buildSqlOrder($Order);
		}

		$Sql .= ' limit '
			. $Database->quote($Offset, 'INTEGER')
			. ', ' . $Database->quote($Limit, 'INTEGER');

		$Sql .= ';';

		$Result = $Database->query($Sql);
		if ( $Database->isError($Result) )
		{
			throw new Metacoon_Net_Smtp_Transport_Exception('Error inserting into message-repository.');
		}

		$Data = array();
		while( $Row = $Result->fetchRow(MDB2_FETCHMODE_ASSOC) )
		{
			$ItemId = $Row[self::COLUMN_ITEM_ID];
			$Message = $Row[self::COLUMN_MESSAGE];
			$Sender = $Row[self::COLUMN_SENDER];
			$Recipients = $Row[self::COLUMN_RECIPIENTS];
			$FailedDeliveryAttempts = $Row[self::COLUMN_FAILED_DELIVERY_ATTEMPTS];
			$LastDeliveryAttempt = $Row[self::COLUMN_LAST_DELIVERY_ATTEMPT];

			$Recipients = unserialize(base64_decode($Recipients));

			$Data[] = array(
				Metacoon_Net_Smtp_Queue_Item_Interface::ITEM_ID => $ItemId,
				Metacoon_Net_Smtp_Queue_Item_Interface::MESSAGE => $Message,
				Metacoon_Net_Smtp_Queue_Item_Interface::SENDER => $Sender,
				Metacoon_Net_Smtp_Queue_Item_Interface::RECIPIENTS => $Recipients,
				Metacoon_Net_Smtp_Queue_Item_Interface::FAILED_DELIVERY_ATTEMPTS => $FailedDeliveryAttempts,
				Metacoon_Net_Smtp_Queue_Item_Interface::LAST_DELIVERY_ATTEMPT => $LastDeliveryAttempt,
			);
		}

		return $Data;
	}

	/**
	 * (non-PHPdoc)
	 *  @see Metacoon_Net_Smtp_Queue_Repository_Interface::update()
	 */
	public function update(
		array $Constraints,
		$Message,
		$Sender,
		array $Recipients,
		$FailedDeliveryAttempts,
		$LastDeliveryAttempt)
	{
		$Database = $this->getDatabase();
		if ( ! $Database )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('No database set.');
		}

		if ( empty($Constraints) )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('No constraints given.');
		}

		$Recipients = base64_encode(serialize($Recipients));

		$Sql = 'update ' . $Database->quoteIdentifier($this->getTableName())
			. ' set '
			. $Database->quoteIdentifier(self::COLUMN_MESSAGE) . ' = ' . $Database->quote($Message, 'TEXT')
			. ', ' . $Database->quoteIdentifier(self::COLUMN_SENDER) . ' = ' . $Database->quote($Sender, 'TEXT')
			. ', ' . $Database->quoteIdentifier(self::COLUMN_RECIPIENTS) . ' = ' . $Database->quote($Recipients, 'TEXT')
			. ', ' . $Database->quoteIdentifier(self::COLUMN_FAILED_DELIVERY_ATTEMPTS) . ' = ' . $Database->quote($FailedDeliveryAttempts, 'INTEGER')
			. ', ' . $Database->quoteIdentifier(self::COLUMN_LAST_DELIVERY_ATTEMPT) . ' = ' . $Database->quote($LastDeliveryAttempt, 'INTEGER')

			. ' where ' . $this->buildSqlConstraints($Constraints);

		$Result = $Database->query($Sql);
		if ( $Database->isError($Result) )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('Error executing SQL "'.$Sql.'"');
		}

		return 3; // $Result->affectedRows();
	}

	/**
	 * (non-PHPdoc)
	 * @see Metacoon_Net_Smtp_Queue_Constraint::delete()
	 */
	public function delete(array $Constraints)
	{
		$Database = $this->getDatabase();
		if ( ! $Database )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('No database set.');
		}

		if ( empty($Constraints) )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('No conditions given.');
		}

		$Sql = 'delete from ' . $Database->quoteIdentifier($this->getTableName());
		$Sql .= ' where ' . $this->buildSqlConstraints($Constraints);

		$Result = $Database->query($Sql);
		if ( $Database->isError($Result) )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('Error executing SQL "'.$Sql.'"');
		}

		return $Result->affectedRows();
	}

	/**
	 * (non-PHPdoc)
	 * @see Metacoon_Net_Smtp_Queue_Constraint::count()
	 */
	public function count(array $Constraints)
	{
		$Database = $this->getDatabase();
		if ( ! $Database )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('No database set.');
		}

		$Sql = 'select'
			.' count('. $Database->quoteIdentifier(self::COLUMN_ITEM_ID) .')'
			.' from '
			. $Database->quoteIdentifier($this->getTableName());

		if ( ! empty($Constraints) )
		{
			$Sql .= ' where ' . $this->buildSqlConstraints($Constraints);
		}

		$Result = $Database->query($Sql);
		if ( $Database->isError($Result) )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('Error executing query "'.$Sql.'".');
		}

		$Count = $Database->fetchOne($Result);
		if ( false === $Count )
		{
			throw new Metacoon_Net_Smtp_Queue_Exception('Error fetching result.');
		}

		return $Count;
	}

	/**
	 *
	 * @var MDB2_Driver_Common
	 */
	private $Database = null;

	/**
	 *
	 *
	 * @param MDB2_Driver_Common $Database
	 * @return Metacoon_Net_Smtp_Transport_Queue_MDB2_Driver_Common
	 */
	public function setDatabase(MDB2_Driver_Common $Database)
	{
		$this->Database = $Database;
		return $this;
	}

	/**
	 *
	 * @return MDB2_Driver_Common
	 */
	protected function getDatabase()
	{
		return $this->Database;
	}

}
