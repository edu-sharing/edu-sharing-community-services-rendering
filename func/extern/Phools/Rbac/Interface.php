<?php

/**
 *
 *
 *
 */
interface Phools_Rbac_Interface
extends Phools_Acl_Interface
{

	/**
	 *
	 * @param Phools_Acl_Role_Interface $ParentRole
	 * @param Phools_Acl_Role_Interface $Role
	 *
	 * @return Phools_Acl_Interface
	 */
	public function setParentRole(
		Phools_Acl_Role_Interface $ParentRole,
		Phools_Acl_Role_Interface $Role);

	/**
	 *
	 * @param Phools_Acl_Role_Interface $Role
	 *
	 * @return array
	 */
	public function getParentRoles(
		Phools_Acl_Role_Interface $Role);

	/**
	 *
	 * @param Phools_Acl_Role_Interface $ChildRole
	 * @param Phools_Acl_Role_Interface $Role
	 *
	 * @return Phools_Acl_Interface
	 */
	public function appendChildRole(
		Phools_Acl_Role_Interface $ChildRole,
		Phools_Acl_Role_Interface $Role);

	/**
	 *
	 * @param Phools_Acl_Role_Interface $Role
	 *
	 * @return array
	 */
	public function getChildRoles(
		Phools_Acl_Role_Interface $Role);

}

