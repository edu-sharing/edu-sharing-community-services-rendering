<?php


class ESContentNode {

    private $data;

    public function __construct($data) {
        $this -> data = $data;
    }

    public function getData() {
        return $this -> data;
    }

    public function getNode() {
        return $this -> data -> node;
    }

    public function getProperties() {
        return $this -> data -> properties;
    }
    
    public function getProperty($key) {
        if(property_exists ($this -> data -> properties, $key)) {
            if (is_array($this->data->properties->$key) && count($this->data->properties->$key) == 1)
                return $this->data->properties->$key[0];
            return $this->data->properties->$key;
        }
        return false;
    }
    
}