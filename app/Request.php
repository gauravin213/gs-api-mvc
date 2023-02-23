<?php
namespace App;

class Request
{
	function __construct(){}
	public static function all(){
		$data_arr = json_decode(file_get_contents("php://input"), true);
		$data = (!empty($data_arr))? $data_arr : [];
		$requestII = array_merge($_REQUEST, $data);
		return $requestII;
	}
	public static function post(){
		$data_arr = json_decode(file_get_contents("php://input"), true);
		$data = (!empty($data_arr))? $data_arr : [];
		$requestII = array_merge($_POST, $data);
		return $requestII;
	}
	public static function param(){
		$requestII = $_GET;
		return $requestII;
	}
}