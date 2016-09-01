<?php

/**
 *
 *
 *
 */
interface Phools_Acl_Interface
{

	/**
	 * Test if $Role has $Permission on $Subject
	 *
	 * @param Phools_Acl_Permission_Interface $Permission
	 * @param Phools_Acl_Role_Interface $Role
	 * @param Phools_Acl_Subject_Interface $Subject
	 *
	 * @return bool
	 */
	public function allows(
		Phools_Acl_Permission_Interface $Permission,
		Phools_Acl_Role_Interface $Role,
		Phools_Acl_Subject_Interface $Subject);

	/**
	 * Get all permissions a $Role has on $Subject.
	 *
	 * @param Phools_Acl_Role_Interface $Role
	 * @param Phools_Acl_Subject_Interface $Subject
	 *
	 * @return array
	 */
	public function getPermissions(
		Phools_Acl_Role_Interface $Role,
		Phools_Acl_Subject_Interface $Subject);

	/**
	 * Grant a $Role the $Permission for $Subject.
	 *
	 * @param Phools_Acl_Permission_Interface $Permission
	 * @param Phools_Acl_Role_Interface $Role
	 * @param Phools_Acl_Subject_Interface $Subject
	 *
	 * @throws Phools_Acl_Exception
	 *
	 * @return Phools_Acl_Interface
	 */
	public function grant(
		Phools_Acl_Permission_Interface $Permission,
		Phools_Acl_Role_Interface $Role,
		Phools_Acl_Subject_Interface $Subject);

	/**
	 * Revoke a $Role's previously granted $Permission for $Subject.
	 *
	 * @param Phools_Acl_Permission_Interface $Permission
	 * @param Phools_Acl_Role_Interface $Role
	 * @param Phools_Acl_Subject_Interface $Subject
	 *
	 * @throws Phools_Acl_Exception
	 *
	 * @return Phools_Acl_Interface
	 */
	public function revoke(
		Phools_Acl_Permission_Interface $Permission,
		Phools_Acl_Role_Interface $Role,
		Phools_Acl_Subject_Interface $Subject);

	/**
	 *
	 * @param Phools_Acl_Role_Interface $Role
	 *
	 * @return Phools_Acl_Interface
	 */
	public function addRole(Phools_Acl_Role_Interface $Role);

}
