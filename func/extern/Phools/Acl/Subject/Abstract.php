<?php

/**
 *
 *
 *
 */
abstract class Phools_Acl_Subject_Abstract
implements Phools_Acl_Subject_Interface
{

	/**
	 *
	 * @param string $AclSubjectIdentifier
	 */
	public function __construct($AclSubjectIdentifier)
	{
		$this->setAclSubjectIdentifier($AclSubjectIdentifier);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->AclSubjectIdentifier = null;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $AclSubjectIdentifier = '';

	/**
	 *
	 *
	 * @param string $AclSubjectIdentifier
	 * @return Phools_Acl_Subject_Abstract
	 */
	protected function setAclSubjectIdentifier($AclSubjectIdentifier)
	{
		$this->AclSubjectIdentifier = (string) $AclSubjectIdentifier;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getAclSubjectIdentifier()
	{
		return $this->AclSubjectIdentifier;
	}

}
