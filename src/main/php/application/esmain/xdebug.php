<?php
setcookie('XDEBUG_SESSION', 'PHPSTORM');
$requestedUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "&XDEBUG_SESSION_START=PHPSTORM";
$requestedUrl = str_replace('xdebug.php', 'index.php', $requestedUrl);
header('Location: ' . $requestedUrl);
exit();
