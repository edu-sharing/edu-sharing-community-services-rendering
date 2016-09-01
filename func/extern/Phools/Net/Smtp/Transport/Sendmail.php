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
 * Transport a mail by invoking given executable, e.g. "/usr/sbin/sendmail".
 *
 *
 */
class Phools_Net_Smtp_Transport_Sendmail
implements
	Phools_Net_Smtp_Transport_Interface
{

	/**
	 *
	 * @param string $Executable
	 * @param array $Arguments
	 */
	public function __construct(
		$Executable = '/usr/sbin/sendmail',
		array $Arguments = array())
	{
		$this->setExecutable($Executable);

		if ( 0 < count($Arguments) )
		{
			foreach( $Arguments as $Name => $Value )
			{
				$this->addArgument($Name, $Value);
			}
		}
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Arguments = null;
		$this->Executable = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Transport_Interface::send()
	 */
	public function send(Phools_Net_Smtp_Message_Interface $Message, $From, array $To)
	{
		$Command = $this->getExecutable();

		$Arguments = $this->getArguments();
		if ( 0 < count($Arguments) )
		{
			foreach( $Arguments as $Name => $Value )
			{
				$Command .= ' ' . $Name . ' ' . $Value;
			}
		}

		$Command .= ' ' . $Message;

		exec($Command);

		return $this;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Executable = '';

	/**
	 *
	 *
	 * @param string $Executable
	 * @return Phools_Net_Smtp_Transport_Sendmail
	 */
	public function setExecutable($Executable)
	{
		if ( ! is_executable($Executable) )
		{
			throw new Phools_Net_Smtp_Exception('Executable "'.$Executable.'" not found or not executeable.');
		}

		$this->Executable = (string) $Executable;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getExecutable()
	{
		return $this->Executable;
	}

	/**
	 *
	 *
	 * @var array
	 */
	private $Arguments = array();

	/**
	 *
	 *
	 * @param array $Arguments
	 * @return Phools_Net_Smtp_Transport_Sendmail
	 */
	public function addArgument($Name, $Value = '')
	{
		$this->Arguments[$Name] = $Argument;

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return $this->Arguments;
	}

}
