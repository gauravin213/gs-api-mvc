<?php

namespace App;

class Respnse
{
	function __construct(){}
	public static function json($res){
		echo json_encode($res);
	}
}