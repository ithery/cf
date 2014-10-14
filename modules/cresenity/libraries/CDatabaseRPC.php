<?php defined('SYSPATH') OR die('No direct access allowed.');



require_once dirname(__FILE__)."/Lib/json.php";
require_once dirname(__FILE__)."/Lib/json-rpc.php";

class CDatabaseRPC  {
	public static function factory() {
		return new CDatabaseRPC();
	}
	public function query($query) {
		$db = CDatabase::instance();
		if (preg_match("/create|drop/", $query)) {
		  throw new Exception("Sorry you are not allowed to execute '" . 
							  $query . "'");
		}
		/*
		if (!preg_match("/(select.*from *test|insert *into *test.*|delete *from *test|update *test)/", $query)) {
		  throw new Exception("Sorry you can't execute '" . $query .
							  "' you are only allowed to select, insert, delete " .
							  "or update 'test' table");
		}
		*/
		if ($res = $db->query($query)) {
			if ($res === true) {
				return true;
			}
			if ($res->count() > 0) {
				foreach($res as $row) {
					$result[] = $row;
				}
				return $result;
			} else {
				return array();
			}
		} else {
			throw new Exception("MySQL Error: " . mysql_error());
		}
	}
	
}