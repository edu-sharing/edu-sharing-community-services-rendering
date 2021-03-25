<?php
 class Admin {
 	
 	
 	public function __construct() {
 		
 		
 		$loginManager = new LoginManager();
 		$loginManager -> checkLogin();
 		
 		
 		switch($_REQUEST['action']) {
 			case 'dbtool':
 				$this -> initDbtool();
 				exit();
 			break;
 			case 'doupdate':
 				$this -> initUpdate(true);
 				exit();
 			break;
 			case 'update':
 				$this -> initUpdate();
 				exit();
            case 'h5ptool':
                $this -> initH5ptool();
                exit();
 			default:
 				$this -> chooseAction();
 		}	
 	}
 	
 	
 	private function initUpdate($do = false) {
 		$updater = new Updater($do);
 	}
 	
 	private function initDbtool() {
 		global $MC_URL;
 		header('Location: ' . $MC_URL . '/admin/vendor/adminer-4.8.0.php', TRUE, 302);
 	}

     private function initH5ptool() {
         global $MC_URL;
         header('Location: ' . $MC_URL . '/admin/h5p/index.php', TRUE, 302);
     }
 	
 	private function chooseAction() {
 		include dirname(__FILE__) . '/../view/choose.phtml';
 	}

 	
 }
