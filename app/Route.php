<?php

namespace App;
use App\Request;
class Route
{	
	private static $handlers;
	private static $notFoundHandler;
	private const METHOD_POST = 'POST';
	private const METHOD_GET = 'GET';
	private static $pre = '';

	public static function get( string $path, $handler): void
	{
		self::addHandler(self::METHOD_GET, $path, $handler);
	}

	public static function post($path, $handler): void
	{
		self::addHandler(self::METHOD_POST, $path, $handler);
	}

	public static function group($prefix, $handler): void
	{
		$pre = self::$pre;
		self::$pre = $pre.$prefix;
		$handler();
		self::$pre = $pre;
	}

	public static function addHandler(string $method, string $path, $handler): void
	{	
		self::$handlers[$method.self::$pre.$path] = [
			'path' => self::$pre.$path,
			'method' => $method,
			'handler' => $handler
		];
	}

	public static function addNotFoundHandler($handler): void
	{
		self::$notFoundHandler = $handler;
	}

	public static function run(){
		$requestUri = parse_url($_SERVER['REQUEST_URI']);
		$requestPath = $requestUri['path'];
		$requestPathArr = explode('api', $requestPath);
		$requestPathArrEnd = end($requestPathArr);
		$path = $requestPathArrEnd;
		$method = $_SERVER['REQUEST_METHOD'];
		$callback = null;
		if (!empty(self::$handlers[$method.$path])) {
			$handler = self::$handlers[$method.$path];
			if ($handler['path'] == $path && $handler['method'] == $method ) {
				$callback = $handler['handler'];
			}else{
				$callback = self::$notFoundHandler;
			}
		}else{
			$callback = self::$notFoundHandler;
		}
		call_user_func_array( $callback, [Request::all()] );
	}
}