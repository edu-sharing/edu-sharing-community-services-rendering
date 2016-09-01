<?php

/**
 *
 *
 *
 */
abstract class Phools_Acl_Permission_Abstract
implements Phools_Acl_Permission_Interface
{

	/**
	 *
	 * @param string $AclPermissionIdentifier
	 */
	public function __construct($AclPermissionIdentifier)
	{
		$this->setAclPermissionIdentifier($AclPermissionIdentifier);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->AclPermissionIdentifier = null;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $AclPermissionIdentifier = '';

	/**
	 *
	 *
	 * @param string $AclPermissionIdentifier
	 * @return Phools_Acl_Permission_Abstract
	 */
	protected function setAclPermissionIdentifier($AclPermissionIdentifier)
	{
		$this->AclPermissionIdentifier = (string) $AclPermissionIdentifier;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getAclPermissionIdentifier()
	{
		return $this->AclPermissionIdentifier;
	}

}
