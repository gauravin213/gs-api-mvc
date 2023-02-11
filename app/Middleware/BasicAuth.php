<?php
namespace App\Middleware;
class BasicAuth{
	function __construct(){
		//echo "<pre>-->"; print_r($_SERVER); echo "</pre>"; die;
	}
	public function handle($router, $next)
	{	

		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
			return;
		}

		$PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
		$PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];

		$HTTP_AUTHORIZATION = $_SERVER['HTTP_AUTHORIZATION'];
		$BASIC_AUTH_USER_NAME = getenv('BASIC_AUTH_USER_NAME'); //getenv(), $_ENV[], $_SERVER[]
		$BASIC_AUTH_PASSWORD = getenv('BASIC_AUTH_PASSWORD');

	    if( $PHP_AUTH_USER != $BASIC_AUTH_USER_NAME || $PHP_AUTH_PW != $BASIC_AUTH_PASSWORD ) {
	        header('Unauthorized 401 ');
	        return;
	    }
	    return $next($router);
	}
}