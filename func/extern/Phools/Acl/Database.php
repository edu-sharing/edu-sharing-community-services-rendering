<?php

/**
 *
 *
 *
 */
class Phools_Acl_Database
implements Phools_Acl_Interface
{

	/**
	 *
	 * @var string
	 */
	const TABLE_ROLE = 'acl_role';

	/**
	 *
	 * @var string
	 */
	const TABLE_SUBJECT = 'acl_subject';

	/**
	 *
	 * @var string
	 */
	const TABLE_PERMISSION = 'acl_subject';

	/**
	 *
	 * @var string
	 */
	const TABLE_ROLE_HAS_PERMISSION_FOR_SUBJECT = 'acl_role_permission_subject';

	/**
	 *
	 * @param Phools_Database_Connection_Interface $Connection
	 */
	public function __construct(Phools_Database_Connection_Interface $Connection)
	{
		$this->setConnection($Connection);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Connection = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Acl_Interface::allows()
	 */
	public function allows(
		Phools_Acl_Permission_Interface $Permission,
		Phools_Acl_Role_Interface $Role,
		Phools_Acl_Subject_Interface $Subject)
	{
		$Connection = $this->getConnection();

		$PermissionId = $Permission->getAclPermissionIdentifier();
		$RoleId = $Role->getAclRoleIdentifier();
		$SubjectId = $Subject->getAclSubjectIdentifier();

		$Select = 'select 1 from '
			. $Connection->quoteIdentifier(self::TABLE_ROLE_HAS_PERMISSION_FOR_SUBJECT)
			.' where '
			. $Connection->quoteIdentifier('permission_id') .' = '
			. $Connection->quote($PermissionId)
			.' and '
			. $Connection->quoteIdentifier('role_id') . ' = '
			. $Connection->quote($RoleId)
			.' and '
			. $Connection->quoteIdentifier('subject_id') .' = '
			. $Connection->quote($SubjectId)
			.';';

		$Result = $Connection->query($Select);
		if ( $Result->isError() )
		{
			return false;
		}

		$Row = $Result->fetchRow();
		if ( $Row )
		{
			return true;
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Acl_Interface::getPermissions()
	 */
	public function getPermissions(
		Phools_Acl_Role_Interface $Role,
		Phools_Acl_Subject_Interface $Subject)
	{
		$Connection = $this->getConnection();

		$RoleId = $Role->getAclRoleIdentifier();
		$SubjectId = $Subject->getAclSubjectIdentifier();

		$Select = 'select '
			. $Connection->quoteidentifier('permission_id')
			.' from '
			. $Connection->quoteidentifier(self::TABLE_ROLE_HAS_PERMISSION_FOR_SUBJECT)
			.' where '
			. $Connection->quoteidentifier('role_id') . ' = '
			. $Connection->quote($RoleId)
			.' and '
			. $Connection->quoteidentifier('subject_id') . ' = '
			. $Connection->quote($SubjectId)
			.';';

		$Permissions = array();

		$Result = $Connection->query($Select);
		if ( $Result->isError() )
		{
			return $Permissions;
		}

		while( $Row = $Result->fetchRow() )
		{
			$Permissions[] = new Phools_Acl_Permission_Default($Row['permission_id']);
		}

		return $Permissions;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Acl_Interface::grant()
	 */
	public function grant(
		Phools_Acl_Permission_Interface $Permission,
		Phools_Acl_Role_Interface $Role,
		Phools_Acl_Subject_Interface $Subject)
	{
		$Connection = $this->getConnection();

		$PermissionId = $Permission->getAclPermissionIdentifier();
		$RoleId = $Role->getAclRoleIdentifier();
		$SubjectId = $Subject->getAclSubjectIdentifier();

		$Insert = 'insert into '
			. $Connection->quoteidentifier(self::TABLE_ROLE_HAS_PERMISSION_FOR_SUBJECT)
			.' ('
			. $Connection->quoteidentifier('role_id')
			.', ' . $Connection->quoteidentifier('permission_id')
			.', ' . $Connection->quoteidentifier('subject_id')
			.') values('
			. $Connection->quote($RoleId)
			.', ' . $Connection->quote($PermissionId)
			.', ' . $Connection->quote($SubjectId)
			.');';

		$Result = $Connection->query($Insert);
		if ( $Result->isError() )
		{
			return false;
		}

		if ( $Result->affectedRows() < 1 )
		{
			return false;
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Acl_Interface::grant()
	 */
	public function revoke(
		Phools_Acl_Permission_Interface $Permission,
		Phools_Acl_Role_Interface $Role,
		Phools_Acl_Subject_Interface $Subject)
	{
		$Connection = $this->getConnection();

		$PermissionId = $Permission->getAclPermissionIdentifier();
		$RoleId = $Role->getAclRoleIdentifier();
		$SubjectId = $Subject->getAclSubjectIdentifier();

		$Delete = 'delete from '
			. $Connection->quoteidentifier(self::TABLE_ROLE_HAS_PERMISSION_FOR_SUBJECT)
			.' where '
			. $Connection->quoteidentifier('permission_id') . ' = '
			. $Connection->quote($PermissionId)
			.' and '
			. $Connection->quoteIdentifier('role_id') . ' = '
			. $Connection->quote($RoleId)
			.' and '
			. $Connection->quoteIdentifier('subject_id') .' = '
			. $Connection->quote($SubjectId)
			.';';

		$Result = $Connection->query($Delete);
		if ( $Result->isError() )
		{
			return false;
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Acl_Interface::addRole()
	 */
	public function addRole(
		Phools_Acl_Role_Interface $Role)
	{
		$Connection = $this->getConnection();

		$RoleId = $Role->getAclRoleIdentifier();

		$Insert = 'insert into '
			. $Connection->quoteidentifier(self::TABLE_ROLE)
			.' ('
			. $Connection->quoteidentifier('id')
			. ') values ('
			. $Connection->quote($RoleId)
			. ');';

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Database_Connection_Interface
	 */
	private $Connection = null;

	/**
	 *
	 *
	 * @param Phools_Database_Connection_Interface $Connection
	 * @return Phools_Acl_Database
	 */
	protected function setConnection(Phools_Database_Connection_Interface $Connection)
	{
		$this->Connection = $Connection;
		return $this;
	}

	/**
	 *
	 * @return Phools_Database_Connection_Interface
	 */
	protected function getConnection()
	{
		if ( ! $this->Connection )
		{
			throw new Phools_Exception_NotFound('No database-connection set.');
		}

		return $this->Connection;
	}

}
