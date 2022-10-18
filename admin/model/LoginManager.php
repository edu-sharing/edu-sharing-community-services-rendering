<?php


class Loginmanager {
	
	public function __construct() {

	}
	
	public function checkLogin() {
		include dirname(__FILE__) . '/../../conf/db.conf.php';
		define('USERNAME', $dbuser);
		define('PASSWORD_MD5', md5($pwd));
		define('SESSION_LIFETIME_MINUTES', 30);

		$showLoginError = false;
        $showTimeout = false;

		if(!empty($_SESSION['expire']) && time() > $_SESSION['expire']) {
			$_SESSION['loggedin'] = 0;
			$showTimeout = true;
		}
		
		if(isset($_GET['logout'])) {
			$_SESSION['loggedin'] = 0;
		}
		
		if (isset($_GET['login'])) {
						
			if ($_POST['username'] == USERNAME && md5($_POST['password']) == PASSWORD_MD5) {
				$_SESSION['loggedin'] = 1;
				$_SESSION['expire'] = time() + (SESSION_LIFETIME_MINUTES * 60);
			} else {
				$showLoginError = true;
				$_SESSION['loggedin'] = 0;
			}
		}
		
		if(isset($_SESSION['loggedin']) && !$_SESSION['loggedin']) {
			
			$err = '';
	
			if($showLoginError) {
				$err = '<span id="message"><div class="user_message user_error">'.LANG_LOGINERROR.'</div></span>';
			}
			if($showTimeout) {
				$err = '<span id="message"><div class="user_message user_error">'.LANG_TIMEOUT.'</div></span>';
			}
		
			include dirname(__FILE__) . '/../view/LoginForm.phtml';
			
			exit(0);
		}
		
	}
	
	
}