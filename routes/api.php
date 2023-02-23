<?php

use App\Route;
use App\Middleware\BasicAuth;
use App\Middleware\Cors;
use App\Middleware\JwtAuth;
use App\Controllers\GaController;
use App\Controllers\Ga4Controller;
use App\Controllers\LoginController;
use App\Controllers\PostmetaController;

Cors::handle();

Route::get('/', function(){
	echo "Welcome gs core php mvc";
});

BasicAuth::handle(function(){
	
	Route::group('/ga4', function(){
		Route::post('/purchase', function(){
			$Ga4Controller = new Ga4Controller();
			$Ga4Controller->purchase();
		});
	});

	Route::group('/ga', function(){
		Route::post('/purchase', function(){
			$GaController = new GaController();
			$GaController->purchase();
		});
	});

	Route::group('/postmeta', function(){
		Route::post('/add', function(){
			$PostmetaController = new PostmetaController();
			$PostmetaController->add();
		});
	});

});


Route::addNotFoundHandler(function(){
	echo "404 route not found";
});

Route::run();


/*
Route::get('/example1', function(){
	// code here
});

Route::post('/example2', function(){
	// code here
});

Route::group('/group', function(){
	// code here
});

Route::post('/login', function(){
	$LoginController = new LoginController();
	$LoginController->login();
});
JwtAuth::handle(function(){
	// code here
});
BasicAuth::handle(function(){
	// code here
});
*/