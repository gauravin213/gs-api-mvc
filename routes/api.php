<?php

use App\Route;
use App\Middleware\BasicAuth;
use App\Middleware\Cors;
use App\Controllers\ProductController;
use App\Controllers\CategotyController;
use App\Controllers\PostController;
use App\Controllers\PurchaseController;

Cors::handle();

Route::get('/', function(){
	echo "Welcome gs core php mvc";
});

BasicAuth::handle(new Route, function($router){
	Route::post('/purchase', function(){
		$ProductController = new PurchaseController();
		$ProductController->purchase();
	});
});

Route::post('/product', function(){
	$ProductController = new ProductController();
	$ProductController->set_product();
});

Route::get('/product', function(){
	$ProductController = new ProductController();
	$ProductController->get_product();
});

Route::addNotFoundHandler(function(){
	echo "404 route not found";
});

Route::run();