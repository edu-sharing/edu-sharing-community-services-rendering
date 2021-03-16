<?php
class Version {
    public function getInstalledVersion() {

		include(MC_ROOT_PATH . 'conf/db.conf.php');
		if(empty($dsn)) {
		    rename(MC_ROOT_PATH.'conf/db.conf.php', MC_ROOT_PATH.'conf/bk_db.conf.php');
		    include(MC_ROOT_PATH . 'conf/bk_db.conf.php');
		    $str = '';
		    $str .= '<?php' . PHP_EOL;
		    $str .= '$dsn = "mysql:host='.$dbhost.';port=3306;dbname=' . $db .'";' . PHP_EOL;
		    $str .= '$dbuser = "'.$dbuser.'";' . PHP_EOL;
		    $str .= '$pwd = "'.$pwd.'";' . PHP_EOL;
		    file_put_contents(MC_ROOT_PATH . 'conf/db.conf.php', $str);
		}

		$pdo = RsPDO::getInstance();
		$sql = 'SELECT "VERSION_VNUMBER" FROM "VERSION" ORDER BY "VERSION_ID" DESC';
		$sql = $pdo -> queryLimit($sql, 1, 0);

		$stmt = $pdo -> prepare($sql);
		$stmt -> execute();
		$result = $stmt -> fetchObject();
		error_log($result->VERSION_VNUMBER);
		return $result -> VERSION_VNUMBER;
	}
}
?>