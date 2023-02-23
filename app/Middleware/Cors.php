<?php
namespace App\Middleware;
class Cors{
	public static function handle()
	{	
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
		header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
		header('Access-Control-Allow-Credentials: true');
		header('Content-Type: application/json');
	}
}