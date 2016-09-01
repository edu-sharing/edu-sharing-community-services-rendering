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
abstract class Phools_Net_Smtp_Trace_Abstract
implements Phools_Net_Smtp_Trace_Interface
{

	/**
	 * Garbage collect.
	 *
	 */
	public function __destruct()
	{
		$this->ResentDate = null;
		$this->Recipients = null;
		$this->Ccs = null;
		$this->Bcc = null;
	}

	/**
	 *
	 *
	 * @var DateTime
	 */
	protected $ResentDate = null;

	/**
	 *
	 *
	 * @param DateTime $ResentDate
	 * @return Phools_Net_Smtp_Trace
	 */
	public function setResentDate(DateTime $ResentDate)
	{
		$this->ResentDate = $ResentDate;
		return $this;
	}

	/**
	 *
	 * @return DateTime
	 */
	protected function getResentDate()
	{
		return $this->ResentDate;
	}

	/**
	 *
	 *
	 * @var array
	 */
	private $Recipients = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Trace_Interface::addResentTo()
	 */
	public function addResentTo(Phools_Net_Smtp_Address_Interface $Recipient)
	{
		$this->Recipients[] = $Recipient;
		return $this;
	}

	protected function getRecipients()
	{
		return $this->Recipients;
	}

	/**
	 *
	 *
	 * @var array
	 */
	private $Ccs = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Trace_Interface::addResentCc()
	 */
	public function addResentCc(Phools_Net_Smtp_Address_Interface $Cc)
	{
		$this->Ccs[] = $Cc;
		return $this;
	}

	protected function getResentCc()
	{
		return $this->Ccs;
	}

	/**
	 *
	 *
	 * @var array
	 */
	private $Bcc = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Trace_Interface::addResentBcc()
	 */
	public function addResentBcc(Phools_Net_Smtp_Address_Interface $Bcc)
	{
		$this->Bcc[] = $Bcc;
		return $this;
	}

	protected function getResentBcc()
	{
		return $this->Bcc;
	}

}
