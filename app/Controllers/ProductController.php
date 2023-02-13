<?php

namespace App\Controllers;
use App\Models\Product;
use App\Database;
class ProductController 
{
	function __construct(){}

	public function set_product(){

		$prefix = getenv('DB_PREFIX');
		$res = Database::select("SELECT * FROM {$prefix}posts limit 5");
		//echo "<pre>"; print_r($res); echo "</pre>";
		echo json_encode($res);
		die;

		/*$data = json_decode(file_get_contents("php://input"), true);
		echo json_encode($data);
		exit;*/
		//echo json_encode(['1111-']);
		//exit;
	}

	public function get_product(){

		$prefix = getenv('DB_PREFIX');
		$res = Database::select("SELECT * FROM {$prefix}posts limit 5");
		//echo "<pre>"; print_r($res); echo "</pre>";
		echo json_encode($res);
		die;

		/*$data = json_decode(file_get_contents("php://input"), true);
		echo json_encode($data);
		die;*/
		//echo json_encode(['2222--']);
		//exit;
	}
}

