<?php

if(
	(isset($_POST['session']) && !empty($_POST['session'])) &&
	(isset($_POST['value']) && !empty($_POST['value'])) &&
	(isset($_COOKIE['csrf_token']) && $_POST['csrf_token'] == $_COOKIE['csrf_token'])
) {
	if(session_status() !== PHP_SESSION_ACTIVE) session_start(['cookie_lifetime' => 2592000, 'cookie_secure' => false, 'cookie_httponly' => false]);
	
	$_SESSION[$_POST['session']] = $_POST['value'];
	setcookie('csrf_token', '');
	
	echo json_encode(['success' => true]);
} else {
	echo json_encode(['success' => false]);
}
