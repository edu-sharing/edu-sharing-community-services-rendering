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

    public function getNodeProperty($key) {
        if(property_exists ($this -> data -> node -> properties, $key)) {
            if (is_array($this->data->node -> properties->$key) && count($this->data->node->properties->$key) == 1)
                return $this->data->node -> properties->$key[0];
            return $this->data->node -> properties->$key;
        }
        return false;
    }
    
}