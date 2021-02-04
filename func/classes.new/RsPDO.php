<?php

define("IMPLEMENTED_DRIVERS", array("pgsql", "mysql"));

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
        return self::$instance;
    }
    
    public function querylimit($query, $limit, $offset) {
                return $query . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    public function __construct() {
        include(dirname(__FILE__) . '/../../conf/db.conf.php');
        $this -> dsn = $dsn;
        $this -> dbuser = $dbuser;
        $this -> pwd = $pwd;
        $this -> driver = substr($dsn, 0, strpos($dsn, ':'));
        if(!in_array($this -> driver, IMPLEMENTED_DRIVERS))
            throw new Exception('DB driver invalid or not implemented yet.');
        parent::__construct($dsn, $dbuser, $pwd,
            ($this -> driver === 'mysql') ? array(PDO::ATTR_EMULATE_PREPARES, true, PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="ANSI,NO_KEY_OPTIONS,NO_TABLE_OPTIONS,NO_FIELD_OPTIONS"') : array(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION));
    }
    
    public function getDriver() {
        return $this -> driver;
    }

    private function __clone() {
        
    }

}
