<?php

class Config {

    private static  $values = null;

    public static function get($key, $default = null){
        if(is_null(self::$values)){
            self::$values = array();
        }
        return self::$values[$key] ?? $default;
    }

    public static function set($key, $value){
        if(is_null(self::$values)){
            self::$values = array();
        }
        self::$values[$key] = $value;
    }
}