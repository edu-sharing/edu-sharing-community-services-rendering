<?php

require_once(dirname(__FILE__).'/../Abstract.php');


/**
 * Base-class for all remote-object-handlers. Inheriting handlers must
 * implement handleRemoteObject() to provide remote-specific behaviour.
 *
 *
 */
abstract class ESRender_Plugin_RemoteObject_Abstract
extends ESRender_Plugin_Abstract
{

	/**
	 *
	 * @param string $RepositoryType
	 */
	public function __construct($RepositoryType)
	{
		$this->setRepositoryType($RepositoryType);
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Plugin_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->PropertyRemoteNodeId = null;
		$this->PropertyRemoteRepositoryId = null;
		$this->RepositoryType = null;

		parent::__destruct();
	}

	/**
	 * Overwrite this method in inheriting classes to provide remote-specific
	 * behaviour.
	 *
	 * @param string $RemoteNodeId
	 * @param string $RemoteRepositoryId
	 */
	abstract protected function handleRemoteObject(
		$RemoteNodeId,
		$RemoteRepositoryId);

	/**
	 *
	 *
	 * (non-PHPdoc)
	 * @see ESRender_Plugin_Interface::postLoadRepository()
	 */
	public function postLoadRepository(
		EsApplication &$remote_rep,
		&$app_id,
		&$object_id,
		&$course_id,
		&$resource_id,
		&$username)
	{
		if ( empty($remote_rep->prop_array['repositorytype'] ) )
		{
			trigger_error('No repository-type configured for remote-repository "'.$remote_rep->prop_array['appid'].'".');
		}

		if ( $this->getRepositoryType() == $remote_rep->prop_array['repositorytype'] )
		{
			$this->handleRemoteRepository(
				$object_id,
				$remote_rep->prop_array['appid']);

			return true;
		}

		return false;
	}

	/**
	 *
	 * (non-PHPdoc)
	 * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
	 */
	public function postRetrieveObjectProperties(
		EsApplication &$remote_rep,
		&$app_id,
		Node &$contentNode,
		&$course_id,
		&$resource_id,
		&$username)
	{
		if ($contentNode->getType() != '{http://www.campuscontent.de/model/1.0}remoteobject')
		{
			return false;
		}

		$RemoteNodeId = $contentNode->properties[$this->getPropertyRemoteNodeId()];
		if ( empty($RemoteNodeId) )
		{
			trigger_error('No remote-node-id set.', E_USER_ERROR);
		}

		$RemoteRepositoryId = $contentNode->properties[$this->getPropertyRemoteRepositoryId()];
		if ( empty($RemoteRepositoryId) )
		{
			trigger_error('No remote-repository-id set.', E_USER_ERROR);
		}

		if ( empty($remote_rep->prop_array['repositorytype'] ) )
		{
			trigger_error('No repository-type configured for remote-repository "'.$remote_rep->prop_array['appid'].'".');
		}

		if( $remote_rep->prop_array['repositorytype'] == $this->getRepositoryType() )
		{
			$this->handleRemoteRepository(
				$RemoteNodeId,
				$RemoteRepositoryId);

			return true;
		}

		return false;
	}

	/**
	 * Hold the repository-type we'll handle remotely.
	 *
	 * @var string
	 */
	private $RepositoryType = '';

	/**
	 * Set the remote-repository-type to act upon when found in node-properties.
	 *
	 * @param string $RepositoryType
	 */
	public function setRepositoryType($RepositoryType)
	{
		$this->RepositoryType = (string) $RepositoryType;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getRepositoryType()
	{
		return $this->RepositoryType;
	}

	/**
	 * Holds the name of the property which will provide the remote-node-id.
	 *
	 * @var string
	 */
	private $PropertyRemoteNodeId =
		'{http://www.campuscontent.de/model/1.0}remotenodeid';

	/**
	 * Set the name of property holding the remote-node-id.
	 *
	 * @param string $PropertyName
	 *
	 * @return ESRender_Plugin_RemoteObject_Abstract
	 */
	public function setPropertyRemoteNodeId($PropertyName)
	{
		$this->PropertyRemoteNodeId = (string) $PropertyName;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getPropertyRemoteNodeId()
	{
		return $this->PropertyRemoteRepositoryId;
	}

	/**
	 * The name of the property which will provide the remote-repository-id.
	 *
	 * @var string
	 */
	private $PropertyRemoteRepositoryId =
		'{http://www.campuscontent.de/model/1.0}remoterepositoryid';

	/**
	 * Set the name of property holding the id of remote-repository-id.
	 *
	 * @param string $PropertyName
	 *
	 * @return ESRender_Plugin_RemoteObject_Abstract
	 */
	public function setPropertyRemoteRepositoryId($PropertyName)
	{
		$this->PropertyRemoteRepositoryId = (string) $PropertyName;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getPropertyRemoteRepositoryId()
	{
		return $this->PropertyRemoteRepositoryId;
	}

}
