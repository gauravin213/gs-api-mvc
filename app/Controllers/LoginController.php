<?php

namespace App\Controllers;
use App\Models\Product;
use App\Database as DB;
use App\Request;
use App\Respnse;

use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;
use Firebase\JWT\Key;

class LoginController 
{
	function __construct(){}

	public function login(){
		$request = Request::all();
		//Respnse::json($request);
		//exit;

		$issued_at  = time();
		$not_before = $issued_at;
		$expire     = $issued_at + ( 60 * 60 ); //time + seconds * minuts 
		$payload = array(
		    'iss'  => 'http://127.0.0.1/array/gs-api-mvc/api/product',
		    'iat'  => $issued_at,
		    'nbf'  => $not_before,
		    'exp'  => $expire,
		    'data' => array(
		        'id' => 2,
		    ),
		);
		
		$key = 'gaurav123';
		$token = JWT::encode($payload, $key, 'HS256');
		Respnse::json(['token' => $token]);
		exit;
	}
}