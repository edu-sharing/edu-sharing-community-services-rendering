<?php

/**
 * Build a base-class providing the underlying infrastructure for the actual
 * application-class which will provide neccessary functionality.
 *
 *
 */
abstract class ESRender_Application_Abstract
implements ESRender_Application_Interface {

    /**
     *
     * @var PDO $pdo
     */
    public function __construct(PDO $pdo) {
        $this -> setPdo($pdo);
    }

    /**
     *
     */
    public function __destruct() {
        $this -> Logger = null;
        $this -> pdo = null;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Application_Interface::getDefaultDisplayMode()
     */
    public function getDefaultDisplayMode() {
        return ESRender_Application_Interface::DISPLAY_MODE_WINDOW;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Application_Interface::getDefaultWidth()
     */
    public function getDefaultWidth() {
        return ESRender_Application_Interface::DEFAULT_WIDTH;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Application_Interface::getDefaultHeight()
     */
    public function getDefaultHeight() {
        return ESRender_Application_Interface::DEFAULT_HEIGHT;
    }


    /**
     *
     *
     * @param PDO $pdo
     * @return ESRender_Application_Abstract
     */
    protected function setPdo(PDO $pdo) {
        $this -> pdo = $pdo;
        return $this;
    }

    /**
     *
     * @return PDO
     */
    public function getPdo() {
        return $this -> pdo;
    }

    /**
     * Contains the logger to use, if any has been set.
     *
     * @var Logger
     */
    private $Logger = null;

    /**
     * Set an optional logger.
     *
     * @param Logger $Logger
     * @return ESRender_Application_Abstract
     */
    public function setLogger(Logger $Logger = null) {
        $this -> Logger = $Logger;
        return $this;
    }

    /**
     * Return the current logger, or null if none set.
     *
     * @return Logger
     */
    protected function getLogger() {
        return $this -> Logger;
    }

}
