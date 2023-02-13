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
		self::$servername = getenv('DB_HOST');
		self::$username = getenv('DB_USERNAME');
		self::$password = getenv('DB_PASSWORD');
		self::$dbname = getenv('DB_DATABASE'); 
		self::$prefix = getenv('DB_PREFIX');
		$conn = new mysqli(self::$servername, self::$username, self::$password, self::$dbname);
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}
		return $conn;
	}

	public static function insert($data_array, $tb_name){
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

	public static function update($data_array, $tb_name, $where_arr){
		$result = [];
		$conn = self::conn();
		if (count($data_array)!=0) {
			$columns = array_keys($data_array);
			$values = array_values($data_array);
			$c = implode(", ", $columns);
			$v = "'".implode("', '", $values)."'";
			$count = count($data_array) - 1;
			$c = 0;
			$sub_q = "";
			foreach ($data_array as $key => $value) {
				if ($count == $c) {
					$sub_q .= "{$key}='{$value}'";
				}else{
					$sub_q .= "{$key}='{$value}', ";
				}
				$c++;
			}
			foreach ($where_arr as $key => $value) {
				$q = "UPDATE {$tb_name} SET {$sub_q} WHERE {$key}={$value}"; 
				$result = $conn->query($q);
			}
		}
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
		foreach ($where_arr as $key => $value) {
			$q = "DELETE FROM {$tb_name} WHERE {$key}={$value}";
			$result = $conn->query($q);
		}
		$conn->close();
		return $result;
	}
}

//$res = Database::insert(['fname' => 'Test', 'lanme' => 'Tster'], 'users');
//echo "<pre>"; print_r($res); echo "</pre>";

/*$res = Database::update(['fname' => 'Test22', 'lanme' => 'Tster'], 'users', ['id' => '1']);
echo "<pre>"; print_r($res); echo "</pre>";*/

/*$res = Database::select('SELECT * FROM users');
echo "<pre>"; print_r($res); echo "</pre>";*/

/*$res = Database::delete('users', ['id' => '1']);
echo "<pre>"; print_r($res); echo "</pre>";*/

/*$prefix = getenv('DB_PREFIX');
$res = Database::select("SELECT * FROM {$prefix}posts limit 5");
echo "<pre>"; print_r($res); echo "</pre>";
die;*/