<?php

/**
 * Fully auto translated exception, no detail message
 */
class ESRender_Exception_Generic extends ESRender_Exception_Abstract {
    public static $TYPE_INTERNAL = 'internal';
    public static $TYPE_UNKNOWN = 'unknown';
    public static $TYPE_PERMISSIONS_MISSING = 'permissions_missing';

    private $type;

    /**
     * ESRender_Exception_Generic constructor.
     */
    public function __construct($type = null)
    {
        if(!$type) {
            $type = ESRender_Exception_Generic::$TYPE_UNKNOWN;
        }
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }






}
