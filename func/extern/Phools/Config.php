<?php

/**
 *
 *
 *
 */
class Phools_Config
implements Phools_Config_Interface
{

	/**
	 *
	 *
	 */
	protected $Params = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Config_Interface::hasParam()
	 */
	public function hasParam($Offset) {
		if ( isset($this->Params[$Offset]) ) {
			return true;
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($Offset) {
		return $this->hasParam($Offset);
	}

	/**
	 * Convinience method to allow object-like access.
	 *
	 * @param string $Offset
	 * @return bool
	 */
	public function __isset($Offset) {
		return $this->hasParam($Offset);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Config_Interface::setParam()
	 */
	public function setParam($Offset, $value) {
		$this->Params[$Offset] = (string) $value;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($Offset, $value) {
		return $this->setParam($Offset, $value);
	}


	/**
	 * Convinience method to allow object-like access.
	 *
	 * @param string $Offset
	 * @param string $value
	 * @return Phools_Config
	 */
	public function __set($Offset, $value) {
		return $this->setParam($Offset, $value);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Config_Interface::getParam()
	 */
	public function getParam($Offset) {
		if ( ! $this->hasParam($Offset) ) {
			throw new Phools_Config_ParamNotFoundException('Config-param "'.$Offset.'" not found.');
		}

		return $this->Params[$Offset];
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($Offset) {
		return $this->getParam($Offset);
	}

	/**
	 * Convinience method to allow object-like access.
	 *
	 * @param string $Offset
	 * @return string
	 */
	public function __get($Offset) {
		return $this->getParam($Offset);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Config_Interface::unsetParam()
	 */
	public function unsetParam($Offset) {
		unset($this->Params[$Offset]);
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($Offset) {
		return $this->unsetParam($Offset);
	}

	/**
	 * Convinience method to allow object-like access.
	 *
	 * @param string $Offset
	 * @return Phools_Config
	 */
	public function __unset($Offset) {
		return $this->unsetParam($Offset);
	}

}
