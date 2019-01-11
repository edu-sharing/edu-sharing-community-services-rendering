<?php

require_once(dirname(__FILE__).'/Abstract.php');
require_once(dirname(__FILE__).'/Exception/NotFound.php');


/**
 *
 *
 */
class ESRender_Plugin_Redirect
extends ESRender_Plugin_Abstract
{

	public function __destruct()
	{
		$this->PropertiesNotEmpty = null;
		$this->WherePropertyEquals = null;

		parent::__destruct();
	}

	/**
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
		$Logger = $this->getLogger();

		// check pre-requisites
		foreach( $this->PropertyEquals as $PropertyName => $Value )
		{
			if ( ! isset($contentNode->properties[$PropertyName]) )
			{
				return true;
			}

			if ( $contentNode->properties[$PropertyName] != $Value )
			{
				return true;
			}
		}

		// check location-property
		if ( empty($this->PropertyLocation) )
		{
			throw new ESRender_Plugin_Exception_NotFound('Location-property not set.');
		}

		if ( ! isset($contentNode->properties[$this->PropertyLocation]) )
		{
			throw new ESRender_Plugin_Exception_NotFound('No location-property "'.$this->PropertyLocation.'" not found.');
		}

		// actually redirect
		$Location = $contentNode->properties[$this->PropertyLocation];
		if ( ! $Location )
		{
			throw new ESRender_Plugin_Exception_NotFound('No location found in property "'.$this->PropertyLocation.'".');
		}

		if ( $Logger )
		{
			$Logger->debug('Redirecting to location: "'.$Location.'".');
		}

		header('HTTP/1.1 302 See Other');
		header('Location: '.$Location);

		exit();
	}

	/**
	 * Holds the name of the property which will provide the location to
	 * redirect to.
	 *
	 * @var string
	 */
	protected $PropertyLocation =
		'cclom:location';

	/**
	 * Set the property to holding the location for redirecting.
	 *
	 * @param string $PropertyName
	 *
	 * @return ESRender_Plugin_Redirect
	 */
	public function setPropertyLocation($PropertyName)
	{
		$this->PropertyLocation = (string) $PropertyName;
		return $this;
	}

	/**
	 * Holds the names of properties which must equal a given value for the
	 * redirection to happen as $PropertyName => $Value.
	 *
	 * @var array
	 */
	protected $PropertyEquals = array();

	/**
	 * Add a property which must equal given value for redirection to occur.
	 *
	 * @param string $PropertyName
	 * @param string $Value
	 *
	 * @return ESRender_Plugin_Redirect
	 */
	public function whereEquals($PropertyName, $Value)
	{
		$this->PropertyEquals[$PropertyName] = (string) $Value;
		return $this;
	}

}
