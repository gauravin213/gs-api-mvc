<?php

namespace App\Controllers;
use App\Database as DB;
use App\Request;
use App\Respnse;
use App\Models\Postmeta;
class PostmetaController 
{
	function __construct(){}

	public function add(){
		$body = Request::all();
		$post_id = $body['post_id'];
		$meta_key = $body['meta_key'];
		$meta_value = $body['meta_value'];
		$Postmeta =  Postmeta::update_post_meta($post_id, $meta_key, $meta_value);
        Respnse::json($Postmeta);
        exit;
	}
}