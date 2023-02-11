<?php

namespace App;

class Route
{	
	private array $handlers;
	private $notFoundHandler;
	private const METHOD_POST = 'POST';
	private const METHOD_GET = 'GET';

	public function get( string $path, $handler): void
	{
		$this->addHandler(self::METHOD_GET, $path, $handler);
	}

	public function post($path, $handler): void
	{
		$this->addHandler(self::METHOD_POST, $path, $handler);
	}

	public function addHandler(string $method, string $path, $handler): void
	{
		$this->handlers[$method.$path] = [
			'path' => $path,
			'method' => $method,
			'handler' => $handler
		];
	}

	public function addNotFoundHandler($handler): void
	{
		$this->notFoundHandler = $handler;
	}

	public function run(){

		$requestUri = parse_url($_SERVER['REQUEST_URI']);
		$requestPath = $requestUri['path'];

		//filter request uri path
		$requestQuery = (!isset( $requestUri['query'])) ? '' : $requestUri['query'];
		$requestPathArr = explode('api', $requestPath);
		$requestPathArrEnd = end($requestPathArr);
		$requestPath = $requestPathArrEnd;
	
		$method = $_SERVER['REQUEST_METHOD'];
		$callback = null;
		foreach ($this->handlers as $handler) {
			if ($handler['path'] == $requestPath && $method == $handler['method'] ) {
				$callback = $handler['handler'];
			}
		}
		if (!$callback) {
			header('HTTP/1.0 404 Not Found');
			if (!empty($this->notFoundHandler)) {
				$callback = $this->notFoundHandler;
			}
		}
		call_user_func_array($callback, [array_merge($_GET, $_POST)]);
	}
}


/*$router = new Route();

$router->get('/array/api-mvc/test', function(){
	echo "Hellow test get";
});

$router->get('/array/api-mvc/test2', function(){
	echo "<pre>_POST: "; print_r($_POST); echo "</pre>";
	echo "<pre>_GET: "; print_r($_GET); echo "</pre>";
	echo "<pre>file_get_contents: "; print_r( file_get_contents("php://input") ); echo "</pre>";
	$data = json_decode(file_get_contents("php://input"), true);
	echo "<pre>json: "; print_r($data); echo "</pre>";
	exit;
});

$router->post('/array/api-mvc/test2', function(){
	echo "<pre>_POST: "; print_r($_POST); echo "</pre>";
	echo "<pre>_GET: "; print_r($_GET); echo "</pre>";
	echo "<pre>file_get_contents: "; print_r( file_get_contents("php://input") ); echo "</pre>";
	$data = json_decode(file_get_contents("php://input"), true);
	echo "<pre>json: "; print_r($data); echo "</pre>";
	exit;
});

$router->addNotFoundHandler(function(){
	echo "page not nout 404";
});

$router->run();*/