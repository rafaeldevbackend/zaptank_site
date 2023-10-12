<?php
 
if(isset($_COOKIE['csrf_token']) && $_POST['csrf_token'] == $_COOKIE['csrf_token']) {
	
	if(session_status() !== PHP_SESSION_ACTIVE) session_start(['cookie_lifetime' => 2592000, 'cookie_secure' => false, 'cookie_httponly' => false]);	
	 
	setcookie(
		$name = 'jwt_authentication_hash', 
		$value = $_POST['jwt_authentication_hash'], 
		$expires = time() + (60*60*24*30), 
		$path = '/'/*, 
		$domain = 'http://localhost', 
		$secure = true, 
		$httponly = true*/
	);
	$_SESSION['UserId'] = $_POST['user_id'];
	$_SESSION['UserName'] = $_POST['email'];
	$_SESSION['PassWord'] = $_POST['password'];
	$_SESSION['Telefone'] = $_POST['phone'];
	$_SESSION['Status'] = "Conectado"; 
	$_SESSION['verifiedEmail'] = $_POST['verified_email'];
	$_SESSION['opinion'] = $_POST['opinion'];
	$_SESSION['badMail'] = $_POST['badMail'];
	$_SESSION['isFirstCharge'] = $_POST['isFirstCharge'];
	setcookie('csrf_token', '');
	 
	echo json_encode(['success' => true]);	
} else {
	echo json_encode(['success' => false]);
}
 
