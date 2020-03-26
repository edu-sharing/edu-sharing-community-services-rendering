<?php

class Updater {
	
	public $installedVersion = '';
    public $updateVersion = '';
	
	public function __construct($do = null) {
		include dirname(__FILE__) . '/../update/update.php';
		$this -> installedVersion = $this -> getinstalledversion();
		$this -> updateVersion = UPDATEVERSION;
		if($do) {
			if($this -> isUpdatable())
				$updated = $this -> update();
			if($updated)
				$this -> showUpdated();
			else
				$this -> showUpdateError();
		} else {
			$this -> show();
		}		
	}
	
	private function showUpdateError() {
		include dirname(__FILE__) . '/../view/updateError.phtml';
	}
	
	private function show() {
		include dirname(__FILE__) . '/../view/update.phtml';
	}
	
	private function showUpdated() {
		include dirname(__FILE__) . '/../view/updated.phtml';
	}
	
	
	public function isUpdatable() {
	
		if(version_compare($this -> updateVersion, $this -> installedVersion) > 0)
			return true;
		else
			return false;
	}
	
	public function update() {
		$sucesss = run($this -> installedVersion);
		if($sucesss){
            $success = $this -> setUpdateVersion();
        }
		if($success){
           return true;
        }

		return false;
	}
	
	private function setUpdateVersion() {
		$pdo = RsPDO::getInstance();

        $sql = $pdo -> formatQuery( 'SELECT max(`VERSION_ID`) as max FROM `VERSION`' );
        $stmt = $pdo -> prepare ( $sql );
        $stmt -> execute();
        $result = $stmt -> fetchObject();
        $maxPrimaryKey = $result->max;

        $sql = $pdo -> formatQuery('INSERT INTO `VERSION` (`VERSION_ID`, `VERSION_VNUMBER`, `VERSION_TYPE`) VALUES (:versionid, :version, :type)');
		$stmt = $pdo -> prepare($sql);
        $stmt -> bindValue(':versionid', $maxPrimaryKey + 1);
		$stmt -> bindValue(':version', $this -> updateVersion);
		$stmt -> bindValue(':type', 'update');
		return $stmt -> execute();
	}
	
	private function getInstalledVersion() {
	    $version=new Version();
	    return $version->getInstalledVersion();
	}
	
}
