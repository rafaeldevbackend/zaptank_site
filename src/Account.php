<?php

namespace Zaptank\Site;

use Zaptank\Site\Request;

class Account {
	
	/**
	 * Rota do endpoint de cadastro na api
	*/
	private $registration_route = '/account/new';


	/**
	 * Cadastro de conta
	 * @param string $email
	 * @param string $password
	 * @param string $phone
	 * @param string @ReferenceLocation
	 * @return array $user[]
	*/
	public function register(string $email, $password, string $phone, string $ReferenceLocation) :array {
		
		$request = new Request;
		
		$result = $request->post($this->registration_route, $params = [
			'email' => $email,
			'password' => $password,
			'phone' => $phone,
			'ReferenceLocation' => $ReferenceLocation
		]);
		
		return $result;
	}
	
	
	public function checkEmail($email) :bool {
		
		$request = new Request;
		
		$route = "/account/email/check/" . rawurlencode($email);
		
		$body = $request->get($route);
		
		return $body['response'];
	}
	
	public function checkPhone($phone) :bool {
		
		$request = new Request;
		
		$route = "/account/phone/check/" . rawurlencode($phone);
		
		$body = $request->get($route);
		
		return $body['response'];
	}	
}