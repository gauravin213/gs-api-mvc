<?php

//End point: /api/{rount_name}
use App\Route;
use App\Middleware\BasicAuth;
use App\Database;
use App\Controllers\ProductController;
use App\Controllers\CategotyController;
use App\Controllers\PostController;
use App\Controllers\PurchaseController;

$router = new Route();
$BasicAuth = new BasicAuth;

$router->get('/test', function(){
	echo "Hellow test";
});

$BasicAuth->handle($router, function($router){
	$router->post('/purchase', function(){
		$ProductController = new PurchaseController();
		$ProductController->purchase();
	});
});

$router->post('/product', function(){
	$ProductController = new ProductController();
	$ProductController->set_product();
});

$router->get('/product', function(){
	$ProductController = new ProductController();
	$ProductController->get_product();
});

$router->addNotFoundHandler(function(){
	echo "404 route not found";
});

$router->run();