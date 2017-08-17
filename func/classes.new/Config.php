<?php

class Config {

    private static  $values = null;

    public static function get($key){
        if(is_null(self::$values)){
            self::$values = array();
        }
        return isset(self::$values[$key])?self::$values[$key]:null;
    }

    public static function set($key, $value){
        if(is_null(self::$values)){
            self::$values = array();
        }
        self::$values[$key] = $value;
    }
}