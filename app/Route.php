<?php

namespace App;

class Route
{	
	private static $handlers;
	private static $notFoundHandler;
	private const METHOD_POST = 'POST';
	private const METHOD_GET = 'GET';

	public static function get( string $path, $handler): void
	{
		self::addHandler(self::METHOD_GET, $path, $handler);
	}

	public static function post($path, $handler): void
	{
		self::addHandler(self::METHOD_POST, $path, $handler);
	}

	public static function addHandler(string $method, string $path, $handler): void
	{
		self::$handlers[$method.$path] = [
			'path' => $path,
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
			}
		}
		if (!$callback) {
			header('HTTP/1.0 404 Not Found');
			if (!empty(self::$notFoundHandler)) {
				$callback = self::$notFoundHandler;
			}
		}
		call_user_func_array($callback, [array_merge($_GET, $_POST)]);
	}
}