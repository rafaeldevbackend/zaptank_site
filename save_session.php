 <?php
 
if(isset($_COOKIE['csrf_token']) && $_POST['csrf_token'] == $_COOKIE['csrf_token']) {
	
	if(session_status() !== PHP_SESSION_ACTIVE) session_start(['cookie_lifetime' => 2592000, 'cookie_secure' => true, 'cookie_httponly' => true]);	
	  
	$_SESSION['UserId'] = $_POST['user_id'];
	$_SESSION['UserName'] = $_POST['email'];
	$_SESSION['PassWord'] = $_POST['password'];
	$_SESSION['Telefone'] = $_POST['phone'];
	$_SESSION['Status'] = "Conectado"; 
	 
	echo json_encode(['success' => true]);	
} else {
	echo json_encode(['success' => false]);
}
 
