<?php

namespace Zaptank\Site;

class Request {
	
	/**
	 * Url base de requisição
	*/
	private $api_url = 'http://localhost:8080/zaptank_api';
	
	/**
	 * Requisição curl get
	 * @param string $route
	 * @param array $params[]
	*/		
	public function get(string $route, array $params = []) {
		
		$url = $this->api_url . $route;
		
		// Codifica os parâmetros da URL
		if (!empty($params)) {
			$query = http_build_query($params);
			$url .= '?' . $query;
		}		
		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		curl_close($curl);
		
		return json_decode($response, true);		
	}
	
	/**
	 * Requisição curl post
	 * @param string $route
	 * @param array $params[]
	*/	
	public function post(string $route, array $params = []) {
		
		$url = $this->api_url . $route;
		
		// Codifica os parâmetros da URL
		if (!empty($params)) {
			$query = http_build_query($params);
			$url .= '?' . $query;
		}		
		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
		
		$response = curl_exec($curl);
		curl_close($curl);
		
		return json_decode($response, true);
	}
}