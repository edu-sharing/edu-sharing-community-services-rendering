<?php

/**
 *
 *
 *
 */
abstract class Phools_Acl_Role_Abstract
implements Phools_Acl_Role_Interface
{

	/**
	 *
	 * @param string $AclRoleIdentifier
	 */
	public function __construct($AclRoleIdentifier)
	{
		$this->setAclRoleIdentifier($AclRoleIdentifier);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->AclRoleIdentifier = null;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $AclRoleIdentifier = '';

	/**
	 *
	 *
	 * @param string $AclRoleIdentifier
	 * @return Phools_Acl_Role_Abstract
	 */
	protected function setAclRoleIdentifier($AclRoleIdentifier)
	{
		$this->AclRoleIdentifier = (string) $AclRoleIdentifier;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getAclRoleIdentifier()
	{
		return $this->AclRoleIdentifier;
	}

}
