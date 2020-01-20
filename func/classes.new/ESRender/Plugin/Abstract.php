<?php

require_once(dirname(__FILE__).'/Interface.php');


/**
 * This class implements all of ESRender_Plugin_Interface methods to prevent
 * specialized plugins from having to implement them all over again.
 *
 *
 */
abstract class ESRender_Plugin_Abstract
implements ESRender_Plugin_Interface
{

    /**
     * Free logger
     *
     */
    public function __destruct()
    {
        $this->Logger = null;
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preLoadRepository()
     */
    public function preLoadRepository(
        &$rep_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postLoadRepository()
     */
    public function postLoadRepository(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preCheckTicket()
     */
    public function preCheckTicket(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postCheckTicket()
     */
    public function postCheckTicket(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preRetrieveUserData()
     */
    public function preRetrieveUserData(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postRetrieveUserData()
     */
    public function postRetrieveUserData(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preCheckPermission()
     */
    public function preCheckPermission(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preCheckPermission()
     */
    public function postCheckPermission(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preRetrieveObjectProperties()
     */
    public function preRetrieveObjectProperties(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postRetrieveObjectProperties()
     */
    public function postRetrieveObjectProperties(
        EsApplication &$remote_rep,
        ESContentNode &$ESObject,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preCheckUsage()
     */
    public function preCheckUsage(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postCheckUsage()
     */
    public function postCheckUsage(
        EsApplication &$remote_rep,
        stdClass &$usage,
        &$course_id,
        &$resource_id,
        &$username)
    {
    }
    
    
        /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preSslVerification()
     */
    public function preSslVerification(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username,
        &$homeRep)
    {
    }
    
    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postSslVerification()
     */
    public function postSslVerification(
        EsApplication &$remote_rep,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username,
        &$homeRep)
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preInstanciateObject()
     */
    public function preInstanciateObject()
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postInstanciateObject()
     */
    public function postInstanciateObject()
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preProcessObject()
     */
    public function preProcessObject()
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postProcessObject()
     */
    public function postProcessObject()
    {
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::preTrackObject()
     */
    public function preTrackObject($params = array()) {

    }
    
    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::setTemplate()
     */   
    public function setTemplate(Phools_Template_Script $template) {
        $this -> template = $template;
        return $this;
    }

    /**
     * Hold the optional logger to use.
     *
     * @var Logger
     */
    protected $Logger = null;

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::setLogger()
     */
    public function setLogger(Logger $Logger)
    {
        $this->Logger = $Logger;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::setDefaultLogger()
     */
    public function setDefaultLogger(Logger $Logger)
    {
        if ( null == $this->Logger )
        {
            $this->setLogger($Logger);
        }

        return $this;
    }

    /**
     *
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->Logger;
    }

}
