<?php defined('SYSPATH') OR die('No direct access allowed.');



require_once dirname(__FILE__)."/Lib/json.php";
require_once dirname(__FILE__)."/Lib/json-rpc.php";

class CShellRPC  {
	public static function factory() {
		return new CShellRPC();
	}
	public function shell($command) {
		$commands = explode(" ",$command);
		$cmd = "";
		if(count($commands)>0) {
			$cmd = $commands[0];
		}
		$args = array();
		for($i=1;$i<count($commands);$i++) {
			$args[] = $commands[$i];
		}
		$args_str = implode(" ",$args);
		$ret = csysfunc::execute($cmd, $args_str, $bufr);
		if($ret) {
			return $bufr;
		} else {
			throw new Exception("Something error on shell");
		}
	}
	
}