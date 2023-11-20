<?php

session_start();

if(isset($_SESSION['UserId'])) {
	echo json_encode([
		"authenticated_user" => true
	]);
} else {
	echo json_encode([
		"authenticated_user" => false
	]);	
}