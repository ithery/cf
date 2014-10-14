<?php
class cprogress {
	private static $last_process_id  = "";
	private static function get_process_file($process_id) {
		if($process_id==null||strlen($process_id)==0) {
			if(isset($_POST["cprocess_id"])) {
				$process_id = $_POST["cprocess_id"];
			}
			
		}
		if($process_id==null||strlen($process_id)==0) {
			if(isset($_GET["cprocess_id"])) {
				$process_id = $_GET["cprocess_id"];
			}
		}
		if($process_id==null||strlen($process_id)==0) {
			if(isset($_REQUEST["cprocess_id"])) {
				$process_id = $_REQUEST["cprocess_id"];
			}
		}
		if($process_id==null||strlen($process_id)==0) return false;

		$filename = $process_id;
		$file = ctemp::makepath("process",$process_id.".tmp");
		self::$last_process_id = $process_id;
		return $file;
			
	}
	private static function get_cancel_process_file($process_id) {
		if($process_id==null||strlen($process_id)==0) {
			if(isset($_POST["cprocess_id"])) {
				$process_id = $_POST["cprocess_id"];
			}
			
		}
		if($process_id==null||strlen($process_id)==0) {
			if(isset($_GET["cprocess_id"])) {
				$process_id = $_GET["cprocess_id"];
			}
		}
		if($process_id==null||strlen($process_id)==0) {
			if(isset($_REQUEST["cprocess_id"])) {
				$process_id = $_REQUEST["cprocess_id"];
			}
		}
		if($process_id==null||strlen($process_id)==0) return false;

		$filename = $process_id;
		$file = ctemp::makepath("process",$process_id."_cancel".".tmp");
		return $file;
			
	}
	public static function cancelled($process_id=null) {
		$file = self::get_cancel_process_file($process_id);
		if(file_exists($file)) return true;
		
	}
	public static function set_percent($percent,$info="",$process_id=null) {
		$data = array(
			"percent"=>$percent,
			"info"=>$info,
		);
		$json = json_encode($data);
		$file = self::get_process_file($process_id);
		if($file===false) return false;
		
		file_put_contents($file,$json);
		
		return true;
		
			
	}
	public static function last_process_id() {
		return self::$last_process_id;
	}
}
