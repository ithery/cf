<?php
defined ('SYSPATH') OR die('No direct access allowed.');
class Sync_Controller extends CController {
    public $view;
    public function __construct() {
		parent::__construct();
		set_time_limit(600);
		
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
		
		
		$json=$poststr;
		$lastupdate = "";
		$sess_id = "";
		try {
		
			$json = base64_decode($json);
			//$json = gzinflate($json);
			$obj = json_decode($json,true);
			$store_id = $obj["store_id"];
			$q=  "select last_updated from store where store_id=".$db->escape($store_id);
			$r = $db->query($q);
			if($r->count()>0) {
				$lastupdate = $r[0]->last_updated;
				//create session id for this store
				$sess_id=date("YmdHis");
				$rand="";
				for($i=0;$i<4;$i++){
					$rand =  $rand .  rand(0, 9);  
				}
				$sess_id=$sess_id.$rand;
				$data=array(
				
					"store_id" =>$store_id,
					"app_id" =>1,
					"session_id" =>$sess_id,
					"remote_addr" =>$this->input->ip_address(),
					"user_agent"  =>CF::user_agent(),
					"sync_date_start" =>date('Y-m-d H:i:s'),
				
				);
				$r=$db->insert("log_sync", $data);
				
			} else {
				$error++;
				$error_message="Store not found".$json;
				
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
		} else {
			$data["result"]=0;
			$data["message"]=$error_message;
		
		}
		
		
		
		echo base64_encode((json_encode($data)));
		
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
		
		
		$json=$poststr;
		$lastupdate = "";
		$sess_id = "";
		try {
		
			$json = base64_decode($json,true);
			//$json = gzinflate($json);
			$obj = json_decode($json,true);
			$session_id = $obj["session_id"];
			$q=  "select * from log_sync where session_id=".$db->escape($session_id);
			$r = $db->query($q);
			if($r->count()>0) {
				$row = $r[0];
				$store_id = $row->store_id;
			
				
			} else {
				$error++;
				$error_message="Store not found";
			}
			if($error==0) {
				$statements = $obj["statements"];
				$mysql = "";
				foreach($statements as $s) {
					$mysql.=$s."\r\n";
				}
				$filename = $session_id."_data_".$store_id;
				$path = DOCROOT."temp/";
				if(!is_dir($path)) {
					mkdir($path , 0777);
				}
				$path = $path."sync/";
				if(!is_dir($path)) {
					mkdir($path , 0777);
				}
				$path = $path.date('Ymd')."/";
				if(!is_dir($path)) {
					mkdir($path , 0777);
				}
				
				$error = 0;
				$error_message = "";
				
				try {
					file_put_contents($path.$filename, $mysql);
				} catch(Exception $ex) {
					$error++;
					$error_message = $ex->getMessage();
				}
			}
			
		} catch(Exception $ex) {
			$error++;
			$error_message =$ex->getMessage();
		}
		$data= array();
		if($error==0) {
			$data["result"]=1;
			$data["message"]="OK";
		} else {
			$data["result"]=0;
			$data["message"]=$error_message;
		
		}
		
		
		
		
		echo base64_encode((json_encode($data)));
		
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
		
		
		$json=$poststr;
		$lastupdate = "";
		$session_id = "";
		try {
		
			$json = base64_decode($json,true);
			//$json = gzinflate($json);
			$obj = json_decode($json,true);
			$session_id = $obj["session_id"];
			$lastupdate = $obj["lastupdate"];
			$q=  "select * from log_sync where session_id=".$db->escape($session_id);
			$r = $db->query($q);
			if($r->count()>0) {
				$row = $r[0];
				$store_id = $row->store_id;
				
				$filename="";
				$path = DOCROOT."temp/sync/";
				$path = $path.date('Ymd')."/";
				
				$mysql = "";
				$filename = $session_id."_data_".$store_id;
				if(file_exists($path.$filename)) {
					$mysql = file_get_contents($path.$filename, true);
				}
				$query_arr = explode(";",$mysql);
				foreach($query_arr as $query) {
					if(strlen(trim($query))>0) {
						$db->query($query);
						logger::log("qsync","msg",$query);
					}
				}
				
				$db->query("update store set last_updated=".$db->escape($lastupdate)." where store_id=".$db->escape($store_id));
				
				$data=array(
					"sync_date_finish" =>date('Y-m-d H:i:s'),
				);
				$r=$db->update("log_sync", $data,array("session_id"=>$session_id));
			} else {
				$error++;
				$error_message="Session not found";
			}
		} catch(Exception $ex) {
			$error++;
			$error_message =$ex->getMessage();
		}
		$data= array();
		if($error==0) {
			$data["result"]=1;
			$data["message"]="OK";
		} else {
			$data["result"]=0;
			$data["message"]=$error_message;
		
		}
		
		
		
		echo base64_encode((json_encode($data)));
	}
	
	
}
?>