<?php
namespace App;
use mysqli;
class Database
{
	function __construct()
	{
		//getenv(), $_ENV[], $_SERVER[]
		$this->servername = getenv('DB_HOST');
		$this->username = getenv('DB_USERNAME');
		$this->password = getenv('DB_PASSWORD');
		$this->dbname = getenv('DB_DATABASE'); 
		$this->prefix = getenv('DB_PREFIX');
	}

	public function conn(){
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}
		return $conn;
	}

	public function insert($data_array, $tb_name){
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

	public function update($data_array, $tb_name, $where_arr){
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

	public function select($query){
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

	public function delete($tb_name, $where_arr){
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

//$db = new Database();
//$res = $db->insert(['fname' => 'Test', 'lanme' => 'Tster'], 'users');
//echo "<pre>"; print_r($res); echo "</pre>";

/*$res = $db->update(['fname' => 'Test22', 'lanme' => 'Tster'], 'users', ['id' => '1']);
echo "<pre>"; print_r($res); echo "</pre>";*/

/*$res = $db->select('SELECT * FROM users');
echo "<pre>"; print_r($res); echo "</pre>";*/

/*$res = $db->delete('users', ['id' => '1']);
echo "<pre>"; print_r($res); echo "</pre>";*/

/*$prefix = getenv('DB_PREFIX');
$db = new Database;
$res = $db->select("SELECT * FROM {$prefix}posts limit 5");
echo "<pre>"; print_r($res); echo "</pre>";
die;*/