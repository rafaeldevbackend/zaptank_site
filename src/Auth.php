<?php

namespace Zaptank\Site;

use Zaptank\Site\Request;

class Auth extends Request {
	
	private $authentication_route = '/auth/login';
	
	
	public function login(string $email, $password):array {
		
		$request = new Request;
		
		$result = $request->post($this->authentication_route, $params = [
			'email' => $email,
			'password' => $password
		]);
		
		if($result['success'] == true) {
			
			$data = $result['data'];
			
			$_SESSION['UserName'] = $email;
			$_SESSION['UserId'] = $data['userId'];
            $_SESSION['PassWord'] = $password;
            $_SESSION['Telefone'] = $data['telefone'];
            $_SESSION['Status'] = "Conectado";
            $_SESSION['msg'] = "<div class='alert alert-success ocult-time'>Login bem-sucedido, você será redirecionado em breve...</div>";
			echo "<meta http-equiv='refresh' content='2;url=/selectserver' />";
		} else {
			$_SESSION['msg'] = "<div class='alert alert-danger ocult-time'>{$result['message']}</div>";
		}
		
		return $result;
	}
}