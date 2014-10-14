<?php
defined ('SYSPATH') OR die('No direct access allowed.');
class Sync_Controller extends CController {
    public $view;
    public function __construct() {
		parent::__construct();
		set_time_limit(600);
		
    }
	
	public function index() {
		die("CRESENITY SYNC API SERVICES");
	}
	
	public function initialize() {
		$db = CDatabase::instance();
		$error=0;
		$error_message = "";
		$putdata = fopen( "php://input" , "rb" ); 
		$poststr = "";
		while(!feof( $putdata )) 
			$poststr .=fread($putdata, 4096 ); 
		fclose($putdata);
		/*
		$poststr = '{
			"org_id": 8,
			"store_id": 1
		}';
		*/
		$json=$poststr;
		$lastupdate = "";
		$sess_id = "";
		$forced_datetime = "";
		try {
		
			
			$obj = json_decode($json,true);
			if($obj==null) {
				$error++;
				$error_message = "Not valid JSON Format";
			}
			if($error==0) {
				if(!isset($obj["org_id"])) {
					$error++;
					$error_message = "Parameter org_id not found";
				}
			}
			if($error==0) {
				if(!isset($obj["store_id"])) {
					$error++;
					$error_message = "Parameter store_id not found";
				}
			}
			if($error==0) {
				if(!isset($obj["method"])) {
					$error++;
					$error_message = "Parameter method not found";
				}
			}

			if($error==0) {
				$org_id = $obj["org_id"];
				$store_id = $obj["store_id"];
				$method = $obj["method"];
				$org = corg::get($org_id);
				$store = cstore::get($org_id,$store_id);
				//to get forced date time from database. This setting will overwrite client start date sync
				/*
				$q = "select max(forced_datetime) forced_datetime from sync_force where status = 1 and org_id="
                    .$db->escape($org_id)." and store_id = ".$db->escape($store_id)." and method_name =".$db->escape($obj["method"])."";
				$r = $db->query($q);
                $forced_datetime = $r[0]->forced_datetime;

                $q = "update sync_force set status = 0 where status = 1 and org_id="
                    .$db->escape($org_id)." and store_id = ".$db->escape($store_id)." and method_name =".$db->escape($obj["method"])."";
				
                $db->query($q);
				*/

			}
			if($error==0) {
				if($org==null) {
					$error++;
					$error_message="Org not found";
				}
			}
			if($error==0) {
				if($store==null) {
					$error++;
					$error_message="Store not found";
				}
			}
			if($error==0) {
				//we get last data
				
				$org_code = $org->code;
				$last_sync = cdata::get($method,"last_sync/".$org_code."/".$store_id);
				
			}
			if($error==0) {
				$lastupdate = $last_sync;
				if($lastupdate==null) $lastupdate = "2000-01-01";
				$sess_id=date("YmdHis");
				$rand="";
				for($i=0;$i<4;$i++){
					$rand =  $rand .  rand(0, 9);  
				}
				$sess_id=$sess_id.$rand;
				$data=array(
					
					"org_id" =>$org_id,
					"store_id" =>$store_id,
					"app_id" =>1,
					"session_id" =>$sess_id,
					"method" =>$method,
					"remote_addr" =>$this->input->ip_address(),
					"user_agent"  =>CF::user_agent(),
					"sync_date_start" =>date('Y-m-d H:i:s'),
				
				);
				$r=$db->insert("log_sync", $data);
				
			}
		} catch(Exception $ex) {
			$error++;
			$error_message =$ex->getMessage();
		}
		$data= array();

		if($error==0) {
			$data["result"]=1;
			$data["message"]="OK";
			$data["lastupdate"]=$lastupdate;
			$data["session_id"]=$sess_id;
			$data["org"]=$org->code;
			if(strlen($forced_datetime)>0) $data["forced_datetime"] = $forced_datetime;
		} else {
			$data["result"]=0;
			$data["message"]=$error_message;
		
		}
		
		
		
		echo cjson::encode($data);
		
		//return result as json
	}
	
	public function execute() {
		$db = CDatabase::instance();
		$error=0;
		$error_message = "";
		$putdata = fopen( "php://input" , "rb" ); 
		$poststr = "";
		while(!feof( $putdata )) 
			$poststr .=fread($putdata, 4096 ); 
		fclose($putdata);
		/*
		$poststr = '{
			"session_id": "201401092138341704",
			"statements": []
		}';
		*/
		$json=$poststr;
		$lastupdate = "";
		$sess_id = "";
		try {
		
			$json = gzinflate($json);
			$obj = json_decode($json,true);
			$log = null;
			if($obj==null) {
				$error++;
				$error_message = "Not valid JSON Format";
			}
			if($error==0) {
				if(!isset($obj["session_id"])) {
					$error++;
					$error_message = "Parameter session_id not found";
				}
			}
			if($error==0) {
				if(!isset($obj["statements"])) {
					$error++;
					$error_message = "Parameter statements not found";
				}
			}
			if($error==0) {
				$session_id = $obj["session_id"];
				$statements = $obj["statements"];
				$q=  "select * from log_sync where session_id=".$db->escape($session_id);
				
				$r = $db->query($q);
				if($r->count()>0) {
					$log = $r[0];
				}
			}
			if($error==0) {
				if($log==null) {
					$error++;
					$error_message = "Session ID Invalid";
				}
			}
			if($error==0) {
				$org_id = $log->org_id;
				$store_id = $log->store_id;
				$method = $log->method;
				$org = corg::get($org_id);
				$store = cstore::get($org_id,$store_id);
			}
			if($error==0) {
				if($org==null) {
					$error++;
					$error_message="Org not found";
				}
			}
			if($error==0) {
				if($store==null) {
					$error++;
					$error_message="Store not found";
				}
			}
			
			
			if($error==0) {
				$filename = csync::makepath($org->code,$session_id);
				file_put_contents($filename,$statements);
				/*
				$mysql = "";
				foreach($statements as $s) {
					$mysql.=$s."\r\n";
				}
				
				//we get the full path filename
				$filename = csync::makepath($org->code,$session_id);
				$sql = "";
				if(file_exists($filename)) {
					$sql = file_get_contents($filename);
				}
				$sql.=$mysql;
				
			
				file_put_contents($filename, $sql);
				*/
			}
			
		} catch(Exception $ex) {
			$error++;
			$error_message =$ex->getMessage()."(101)";
		}
		$data= array();
		if($error==0) {
			$data["result"]=1;
			$data["message"]="OK";
			$data["org_code"]=$org->code;
		} else {
			$data["result"]=0;
			$data["message"]=$error_message.'a';
		
		}
		
		
		
		
		echo ((cjson::encode($data)));
		
		//return result as json
	}
	
	public function finalize() {
		$db = CDatabase::instance();
		$error=0;
		$error_message = "";
		$putdata = fopen( "php://input" , "rb" ); 
		$poststr = "";
		while(!feof( $putdata )) 
			$poststr .=fread($putdata, 4096 ); 
		fclose($putdata);
		/*
		$poststr = '{
			"session_id": "201401092138341704"
		}';
		*/
		$json=$poststr;
		$lastupdate = "";
		$session_id = "";
		try {
		
			//$json = gzinflate($json);
			$obj = json_decode($json,true);
			$log = null;
			if($obj==null) {
				$error++;
				$error_message = "Not valid JSON Format";
			}
			if($error==0) {
				if(!isset($obj["session_id"])) {
					$error++;
					$error_message = "Parameter session_id not found";
				}
			}
			
			if($error==0) {
				$session_id = $obj["session_id"];
				$q=  "select * from log_sync where session_id=".$db->escape($session_id);
				$r = $db->query($q);
				if($r->count()>0) {
					$log = $r[0];
				}
			}
			if($error==0) {
				if($log==null) {
					$error++;
					$error_message = "Session ID Invalid";
				}
			}
			if($error==0) {
				$org_id = $log->org_id;
				$store_id = $log->store_id;
				$method = $log->method;
				$org = corg::get($org_id);
				$store = cstore::get($org_id,$store_id);
				$org_code = $org->code;
			}
			if($error==0) {
				if($org==null) {
					$error++;
					$error_message="Org not found";
				}
			}
			if($error==0) {
				if($store==null) {
					$error++;
					$error_message="Store not found";
				}
			}
			if($error==0) {
				$filename = csync::makepath($org->code,$session_id);
				$data = "";
				if(file_exists($filename)) {
					$data = cphp::load_value($filename);
				}
				foreach($data as $statement) {
					$table = $statement['table'];
					$where = $statement['where'];
					$data = $statement['data'];
					$method = $statement['method'];
					switch($method) {
						case "replace":
							if(cdbutils::row_exists($table,$where)) {
								$db->update($table,$data,$where);
							} else {
								$db->insert($table,$data);
							}
						break;
						case "delete":
							$db->delete($table,$where);
						break;
					}
					clogger::log("qsync","msg",$db->last_query());
					
				}
				/*
				$query_arr = explode(";",$sql);
				foreach($query_arr as $query) {
					if(strlen(trim($query))>0) {
						$db->query($query);
						clogger::log("qsync","msg",$query);
					}
				}
				*/
			}
		
			if($error==0) {
				$lastupdate = date("Y-m-d H:i:s");
				//$last_sync = cdata::get($method."_".$store_id,"last_sync/".$org_code);
				cdata::set($method,$lastupdate,"last_sync/".$org_code."/".$store_id);
			}
			if($error==0) {
				$data=array(
					"sync_date_finish" =>date('Y-m-d H:i:s'),
				);
				$r=$db->update("log_sync", $data,array("session_id"=>$session_id));
			}
			
		} catch(Exception $ex) {
			$error++;
			$error_message =$ex->getMessage();
		}
		$data= array();
		if($error==0) {
			$data["result"]=1;
			$data["message"]="OK";
			$data["org_code"]=$org->code;
		} else {
			$data["result"]=0;
			$data["message"]=$error_message;
		
		}
		
		
		
		echo ((cjson::encode($data)));
	}
	
	
}
?>
