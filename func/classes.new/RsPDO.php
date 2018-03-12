<?php

define("IMPLEMENTED_DRIVERS", serialize(array("pgsql", "mysql")));

class RsPDO extends PDO {

    static private $instance = null;
    
    private $dsn = '';
    private $dbuser = '';
    private $pwd = '';
    private $driver = '';

    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$instance;
    }
    
    public function querylimit($query, $limit, $offset) {       
        switch($this -> driver) {
            case 'pgsql':
                return $query . 'LIMIT ' . $limit . 'OFFSET ' . $offset;
            break;
            case 'mysql':
                return $query . 'LIMIT ' . $offset  . ', '. $limit;
            break;
        }    
    }
    
    
    public function formatQuery($query) {
        switch($this -> driver) {
            case 'pgsql':
                return str_replace('`', '"', $query);
            break;
            case 'mysql':
                return $query;
            break;
        }        
    }

    public function __construct() {
        include(dirname(__FILE__) . '/../../conf/db.conf.php');
        $this -> dsn = $dsn;
        $this -> dbuser = $dbuser;
        $this -> pwd = $pwd;
        $this -> driver = substr($dsn, 0, strpos($dsn, ':'));
        if(!in_array($this -> driver, unserialize(IMPLEMENTED_DRIVERS)))
            throw new Exception('DB driver invalid or not implemented yet.');
        parent::__construct($dsn, $dbuser, $pwd);
    }
    
    public function getDriver() {
        return $this -> driver;
    }

    private function __clone() {
        
    }

}
