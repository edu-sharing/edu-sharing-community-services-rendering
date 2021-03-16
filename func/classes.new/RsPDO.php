<?php

define("IMPLEMENTED_DRIVERS", array("pgsql", "mysql"));

class RsPDO extends PDO {

    static private $instance = null;
    
    private $dsn = '';
    private $dbuser = '';
    private $pwd = '';
    private $driver = '';
    private $emulate_prepares = false;

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function querylimit($query, $limit, $offset) {
                return $query . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    public function __construct() {
        include(__DIR__ . '/../../conf/db.conf.php');
        $this -> dsn = $dsn;
        $this -> dbuser = $dbuser;
        $this -> pwd = $pwd;
        if ( isset($prepare) && ($prepare == false)) $this->emulate_prepares = true;
        $this -> driver = substr($dsn, 0, strpos($dsn, ':'));
        if(!in_array($this -> driver, IMPLEMENTED_DRIVERS))
            throw new Exception('DB driver invalid or not implemented yet.');

        $options = [];

        if ($this->driver === 'mysql') {
            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="ANSI,NO_KEY_OPTIONS,NO_TABLE_OPTIONS,NO_FIELD_OPTIONS"',
                             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        }
        if ($this->driver === 'pgsql') {
            $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        }

        parent::__construct($this->dsn, $this->dbuser, $this->pwd, $options);
    }
    
    public function getDriver() {
        return $this -> driver;
    }

    private function __clone() {
        
    }

}
