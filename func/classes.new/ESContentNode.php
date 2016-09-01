<?php


class ESContentNode {

    private $properties = array();

    public function __construct() {

    }
    
    public function setProperties($properties) {
        foreach($properties as $prop) {
            $this -> properties[$prop->key] = $prop -> value;
        }
    }
    
    public function getProperties() {
        return $this -> properties;
    }
    
    public function getProperty($key) {
        if(array_key_exists($key, $this -> properties))
            return $this -> properties[$key];
        return false;
    }
    
}