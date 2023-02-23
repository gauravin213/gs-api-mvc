<?php
namespace App;
use mysqli;
class Database
{
	private static $servername;
	private static $username;
	private static $password;
	private static $dbname;
	private static $prefix;

	public static function conn(){
		//getenv(), $_ENV[], $_SERVER[]
		self::$servername 	= getenv('DB_HOST');
		self::$username 	= getenv('DB_USERNAME');
		self::$password 	= getenv('DB_PASSWORD');
		self::$dbname 		= getenv('DB_DATABASE'); 
		self::$prefix 		= getenv('DB_PREFIX');
		$conn = new mysqli(self::$servername, self::$username, self::$password, self::$dbname);
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}
		return $conn;
	}

	public static function prefix(){
		return getenv('DB_PREFIX');
	}

	public static function insert($tb_name, $data_array){
		$conn = self::conn();
		$last_insert_id = 0;
		if (count($data_array)!=0) {
			$columns = array_keys($data_array);
			$values = array_values($data_array);
			$c = implode(", ", $columns);
			$v = "'".implode("', '", $values)."'";
			$q = "INSERT INTO {$tb_name} ({$c}) VALUES ({$v})";
			$result = $conn->query($q);
			if ($result === TRUE) {
				$last_insert_id = $conn->insert_id;
			}
		}
		$conn->close();
		return $last_insert_id;
	}

	public static function update($tb_name, $data_array, $where_arr){
		$data_array = self::implode_key_value(", ", $data_array, " = ");
		$result = [];
		$conn = self::conn();
		$q = "UPDATE {$tb_name} SET {$data_array} WHERE {$where_arr}"; 
		$result = $conn->query($q);
		$conn->close();
		return $result;
	}

	public static function select($query){
		$response = [];
		$conn = self::conn();
		$result = $conn->query($query);
		if ($result->num_rows > 0) {
		  while($row = $result->fetch_assoc()) {
		    $response[] = $row;
		  }
		}
		$conn->close();
		return $response;
	}

	public static function delete($tb_name, $where_arr){
		$result = [];
		$conn = self::conn();
		$q = "DELETE FROM {$tb_name} WHERE {$where_arr}";
		$result = $conn->query($q);
		$conn->close();
		return $result;
	}

	public static function implode_key_value($separator, $array, $symbol = "=") {
	    return implode(
	        $separator,
	        array_map(
	            function ($k, $v) use ($symbol) {
	                return $k . $symbol . "'".$v."'";
	            },
	            array_keys($array),
	            array_values($array)
	        )
	    );
	}
}

/*

$res = DB::insert("wp_postmeta", ['post_id' => '1367', 'meta_key' => '_payment_method', 'meta_value' => 'cod']);
echo "<pre>"; print_r($res); echo "</pre>";

$res = DB::update(
	'wp_postmeta', 
	[
		'meta_value' => '222222'
	], 
	" post_id='1367' AND meta_key='_payment_method' "
);
echo "<pre>"; print_r($res); echo "</pre>";

$res = DB::delete(
	'wp_postmeta', 
	" post_id='1367' AND meta_key='_payment_method' "
);
echo "<pre>"; print_r($res); echo "</pre>";

$prefix = DB::prefix();
$res = DB::select("SELECT * FROM {$prefix}posts limit 5");
echo "<pre>"; print_r($res); echo "</pre>";

*/