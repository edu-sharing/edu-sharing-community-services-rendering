<?php

/**
 * This product Copyright 2011 metaVentis GmbH.  For detailed notice,
 * see the "NOTICE" file with this distribution.
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

/**
 * This interface defines available callbacks available during the
 * rendering-process. This allows customization of rendering-process
 * withput altering the renderer-source
 *
 * @author doering
 * @copyright metaVentis GmbH, 2011
 */
interface ESRender_Plugin_Interface
{

    /**
     * To be called before loading the requested repository.
     *
     * @param string $rep_id
     * @param string $app_id
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function preLoadRepository(
        &$rep_id,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * To be called after loading the requested repository.
     *
     * @param EsApplication $remote_rep
     * @param string $app_id
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function postLoadRepository(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);


    /**
     * Method to be called before checking the provided ticket.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function preCheckTicket(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Called after checking the validity of provided ticket.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param Node $contentNode
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function postCheckTicket(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Method to be called before retrieving an  user's data from
     * remote-application.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function preRetrieveUserData(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Called after retrieving the user's data from remote-application.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param Node $contentNode
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function postRetrieveUserData(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Method to be called before checking permissions of requesting user.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function preCheckPermission(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Called after checking the permissions of requesting user.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param Node $contentNode
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function postCheckPermission(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Method to be called before retrieving an objects properties from the
     * repository.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function preRetrieveObjectProperties(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Called after retrieving the object's properties from repository.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param Node $contentNode
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function postRetrieveObjectProperties(
        EsApplication &$remote_rep,
        &$app_id,
        ESContentNode &$contentNode,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Method to be called before retrieving an objects usage-information
     * from the repository.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function preCheckUsage(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username);

    /**
     * Called after retrieving the object's usage-information from repository.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param stdClass &$usage,
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function postCheckUsage(
        EsApplication &$remote_rep,
        &$app_id,
        stdClass &$usage,
        &$course_id,
        &$resource_id,
        &$username);
        


    /**
     * Method to be called before ssl verification.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     * @param EsApplication $homeRep
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function preSslVerification(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username,
        &$homeRep);

        
    /**
     * Method to be called after ssl verification.
     *
     * @param EsApplication $remote_rep
     * @param EsApplication $remote_app
     * @param string $object_id
     * @param string $course_id
     * @param string $resource_id
     * @param string $username
     * @param EsApplication $homeRep
     *
     * @throws ESRender_Plugin_Exception_Abstract
     */
    public function postSslVerification(
        EsApplication &$remote_rep,
        &$app_id,
        &$object_id,
        &$course_id,
        &$resource_id,
        &$username,
        &$homeRep);

    /**
     * Called before instanciating an object.
     *
     */
    public function preInstanciateObject();

    /**
     * Called after instanciating an object.
     *
     */
    public function postInstanciateObject();

    /**
     * Called before letting a module render an object.
     *
     */
    public function preProcessObject();

    /**
     * Called after letting a module render an object.
     *
     */
    public function postProcessObject();
    
    /**
     * Called after template initialization
     * 
     */
    public function setTemplate(Phools_Template_Script $template);

    /**
     * Optionally set the logger this plugin shall use. Overwrites
     * setDefaultLogger().
     *
     * @param Logger $Logger
     *
     * @return ESRender_Plugin_Interface
     */
    public function setLogger(Logger $Logger);

    /**
     * Attempt to set the default logger. Skip setting the logger if there is
     * another one already set.
     *
     * @param Logger $Logger
     *
     * @return ESRender_Plugin_Interface
     */
    public function setDefaultLogger(Logger $Logger);

}
