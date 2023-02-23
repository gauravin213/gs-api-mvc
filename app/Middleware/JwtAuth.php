<?php
namespace App\Middleware;
use App\Respnse;

use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;
use Firebase\JWT\Key;

class JwtAuth{
	public static function handle($next)
	{	
		if ( !isset($_SERVER['HTTP_AUTHORIZATION']) || $_SERVER['HTTP_AUTHORIZATION'] == '') {
			return;
		}

		$key = 'gaurav123';
		$HTTP_AUTHORIZATION = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
		$bearer = $HTTP_AUTHORIZATION[0]; 
		$token = $HTTP_AUTHORIZATION[1];

		if( $token != '' && $bearer == 'Bearer') {
			try {
			    $decoded = JWT::decode($token, new Key($key, 'HS256'));
			    if ($decoded->data->id == 2) {
			    	 return $next();
			    }else{
			    	Respnse::json(['Unauthorized 403']); die;
			    }
			} catch (InvalidArgumentException $e) {
			   Respnse::json(['data' => $e->getMessage(), 'exc' => 'InvalidArgumentException']); die;
			} catch (DomainException $e) {
			   Respnse::json(['data' => $e->getMessage(), 'exc' => 'DomainException']); die;
			} catch (SignatureInvalidException $e) {
			   Respnse::json(['data' => $e->getMessage(), 'exc' => 'SignatureInvalidException']); die;
			} catch (BeforeValidException $e) {
			   Respnse::json(['data' => $e->getMessage(), 'exc' => 'BeforeValidException']); die;
			} catch (ExpiredException $e) {
			   Respnse::json(['data' => $e->getMessage(), 'exc' => 'ExpiredException']); die;
			} catch (UnexpectedValueException $e) {
			   Respnse::json(['data' => $e->getMessage(), 'exc' => 'UnexpectedValueException']); die;
			}
	    }else{
			header('Unauthorized 401 ');
	        return;
	    }
	}
}