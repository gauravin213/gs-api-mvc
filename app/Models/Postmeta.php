<?php

namespace App\Models;
use App\Database as DB;
class Postmeta
{
	private static $table = 'postmeta';

	function __construct(){}

	public static function get_post_meta($post_id, $meta_key, $flag = false){
		$response = [];
		$prefix = DB::prefix();
		$tb_name = $prefix.self::$table;
		$result = DB::select(" SELECT meta_value FROM {$tb_name} WHERE post_id='{$post_id}' AND meta_key='{$meta_key}' ");
		if (!empty($result)) {
			if ($flag) {
				$meta_value = $result[0]['meta_value'];
				$data = @unserialize( $meta_value );
				if ($data !== false) {
				   $response = $data;
				} else {
				   $response = $meta_value;
				}
			}else{
				$response = $result;
			}
		}
		return $response;
	}

	public static function add_post_meta($post_id, $meta_key, $meta_value){
		$response = [];
		$prefix = DB::prefix();
		$tb_name = $prefix.self::$table;
		if (is_array($meta_value)) {
			$meta_value = serialize($meta_value);
		}
		$response = DB::insert($tb_name, ['post_id' => $post_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value]);
		return $response;
	}

	public static function update_post_meta($post_id, $meta_key, $meta_value){
		$response = [];
		$prefix = DB::prefix();
		$tb_name = $prefix.self::$table;
		if (is_array($meta_value)) {
			$meta_value = serialize($meta_value);
		}
		$if_exist_meta_key = self::get_post_meta($post_id, $meta_key, true);
		if (!empty($if_exist_meta_key)) {
			$response = DB::update(
				$tb_name, 
				[
					'meta_value' => $meta_value
				], 
				" post_id='{$post_id}' AND meta_key='{$meta_key}' "
			);
		}else{
			$response = self::add_post_meta($post_id, $meta_key, $meta_value);
		}
		return $response;
	}

	public static function delete_post_meta($post_id, $meta_key){
		$response = [];
		$prefix = DB::prefix();
		$tb_name = $prefix.self::$table;
		$response = DB::delete(
			$tb_name, 
			" post_id='{$post_id}' AND meta_key='{$meta_key}' "
		);
		return $response;
	}
}

/*

$Postmeta =  Postmeta::get_post_meta(10, '_xyz', true);
echo "<pre>Postmeta: "; print_r($Postmeta); echo "</pre>";

$Postmeta =  Postmeta::add_post_meta(10, '_xyz', '123');
echo "<pre>Postmeta: "; print_r($Postmeta); echo "</pre>";

$Postmeta =  Postmeta::update_post_meta(10, '_xyz', '123333333');
echo "<pre>Postmeta: "; print_r($Postmeta); echo "</pre>";

$Postmeta =  Postmeta::delete_post_meta(10, '_xyz');
echo "<pre>Postmeta: "; print_r($Postmeta); echo "</pre>";

*/