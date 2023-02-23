<?php
namespace App\Middleware;
class BasicAuth{
	public static function handle($next)
	{	
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
			return;
		}

		$PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
		$PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];
		
		$BASIC_AUTH_USER_NAME = getenv('BASIC_AUTH_USER_NAME'); //getenv(), $_ENV[], $_SERVER[]
		$BASIC_AUTH_PASSWORD = getenv('BASIC_AUTH_PASSWORD');

	    if( $PHP_AUTH_USER != $BASIC_AUTH_USER_NAME || $PHP_AUTH_PW != $BASIC_AUTH_PASSWORD ) {
	        header('Unauthorized 401 ');
	        return;
	    }
	    return $next();
	}
}