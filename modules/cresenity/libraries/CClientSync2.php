<?php

class CClientSync {

	private $data;
	
	private $errors;
	private $session_id;
	private $client_last_update;
	private $delete_records;
	private $last_update_date;
	private $limit_count;
	
	private $after_sync_delete;
	
	private function __construct($data) {
		$this->data =$data;
		$this->errors = array();
		$this->session_id = "";
		$this->delete_records = array();
		$this->last_update_date="";
		$this->limit_count = 50;
		$this->after_sync_delete = array();
		$this->parse_data();
	}
	
	public static function factory($data) {
		return new CClientSync($data);
	}
	
	public function parse_data() {
		foreach($this->data as $module=>$data) {
			if(isset($data["table"])) {
				foreach($data["table"] as $table=>$data_table) {
					if(isset($data_table["after_sync"])) {
						$after_sync = $data_table["after_sync"];
						if(isset($after_sync["delete"])) {
							if($after_sync["delete"]) {
								$this->after_sync_delete[]=$table;
							}
						}
					}
				}
			}
		}
	}
	
	public function get_child_table($table) {
		$childs = array();
		foreach($this->data as $module=>$data) {
			if(isset($data["table"])) {
				foreach($data["table"] as $table_name=>$data_table) {
					if($table_name==$table) {
						if(isset($data_table["child"])) {
							$childs_data = $data_table["child"];
							foreach($childs_data as $k=>$v) {
								$childs[]=$k;
							}
							
						}
					}
				}
			}
		}
		return $childs;
	}
	public function add_error($msg) {
		$this->errors[] = $msg;
		return count($this->errors);
	}
	public function is_error() {
		return count($this->errors)>0;
	}
	public function error_message() {
		$str = "";
		foreach($this->errors as $error) {
			$str.="<p>".$error."</p>";
		}
		return $str;
	}
	
	public function synchronize($module_name) {
		//step : initialize,execute,finalize
		if(!$this->is_error()) {
			$this->initialize($module_name);
		}
		
		if(!$this->is_error()) {
			$this->execute($module_name);
		}
		
		if(!$this->is_error()) {
			$this->finalize($module_name);
		}
		
		return !$this->is_error();
	}
	
			
	private function initialize($method) {
		$app = CApp::instance();
		$org = $app->org();
		$org_id=$org->org_id;
		$store_id = ccfg::get("store_id");
		$initialize_url=ccfg::get('server_synchronize_url').'initialize';
		$postdata = array(
			"org_id"=>$org_id,
			"store_id"=>$store_id,
			"method"=>$method,
		);
		$jsondata = json_encode($postdata);
		$result = $this->http_post($initialize_url,$jsondata);
		$data = cjson::decode($result);
		
		$error=0;
		$error_message = "";
		if($error==0) {
			if(!is_array($data)) {
				$error++;
				$error_message =  "Error on server initialize [11]";
			}
		}
		if($error==0) {
			if(!isset($data['result'])) {
				$error++;
				$error_message = "Error on server initialize [12]";
			}
		}
		
		if($error==0) {
			if($data['result']<=0){
				$error++;
				$error_message=$data['message']."[1]";
			}
		}
		if($error==0) {
			if(isset($data['forced_datetime'])){
				cdata::set($method,$data['forced_datetime'],'client_last_synchronize/'.$org_id.'/'.$store_id);
			}

		}
		if($error==0) {
			$this->client_last_update = cdata::get($method,'client_last_synchronize/'.$org_id.'/'.$store_id);
			if(strlen($this->client_last_update)==0) $this->client_last_update = "2000-01-01 00:00:00";
		}
		if($error==0) {
			if(isset($data['session_id'])){
				$this->session_id = $data['session_id'];
			} else {
				$error++;
				$error_message="Error on server initialize, session_id ot found [13]";
			}
		}
		if($error>0) {
			$this->add_error($error_message);
		}
		
		return $error;
	}
	
	private function insert_statements($table,$row) {
		$db = CDatabase::instance();
		$query="";
		$str_insert_into="";
		$str_value="";
		foreach($row as $k=>$v){
			if($k<>'query_command'){
				$str_insert_into.="`".$k."`,";
				$str_value.=$db->escape($v).",";
			}
		}
		$query.="
			insert ignore into 
				`".$table."`(
				".substr($str_insert_into, 0, strlen($str_insert_into)-1)."
				)
			values(
				".substr($str_value, 0, strlen($str_value)-1)."
			);
		";
		return $query;
	}
	private function update_statements($table,$row) {
		$app = CApp::instance();
		$org = $app->org();
		$org_id=$org->org_id;
		$store_id = ccfg::get("store_id");
		$db = CDatabase::instance();
		$query="";
		$str_set="";
		foreach($row as $k=>$v){
			if($k<>'query_command'){
				$str_set.="`".$k."`=".$db->escape($v).",";
			}
		}
		$query.="
			update  
				`".$table."` 
			set 	
				".substr($str_set, 0, strlen($str_set)-1)."
			where
				org_id=".$org_id."
				and store_id=".$store_id."
				and ".$table."_id=".$row[$table."_id"]."
			;
		";
		return $query;
	}
	public function delete_statements($parent_table_id,$parent_table,$table=null){
		$app = CApp::instance();
		$org = $app->org();
		$org_id=$org->org_id;
		$store_id = ccfg::get("store_id");
		$db = CDatabase::instance();
		$query="";
		$str_set="";
		$str_table=$parent_table;
		if($table){
			$str_table=$table;
		}
		$query.="
			delete from  
				`".$str_table."` 
			where
				org_id=".$org_id."
				and store_id=".$store_id."
				and ".$parent_table."_id=".$parent_table_id."
			;
		";
		return $query;
	}
	private function query_statements($table,$parent_id=null,$parent_table='') {
		$app = CApp::instance();
		$org = $app->org();
		$org_id=$org->org_id;
		$store_id = ccfg::get("store_id");
		$db = CDatabase::instance();
		$data=array();
		$child_table=$this->get_child_table($table);
		
		$last_synchronize_date = $this->client_last_update;
		$query_command_str="
			,case when created > '".$last_synchronize_date."' then 'insert' else 'update' end as query_command 
		";
		$query_where_str=" 
				and( 
					created>'".$last_synchronize_date."'
					or updated>'".$last_synchronize_date."'
				)
				and (
				    created is not null
				    and updated is not null
				)
		";
		if($parent_id){
			$query_command_str=",'insert' as query_command";
			$query_where_str="
				and ".$parent_table."_id=".$parent_id."
			"; 
		}
		switch($table) {
			case "resto_transaction":
				$query_where_str.="
					and is_checkout>0
				";
			break;
		}

        $q="
                select
                    *
                    ".$query_command_str."
                from
                    `".$table."`
                  where
                    org_id=".$org_id."
                    and store_id=".$store_id."
                    ".$query_where_str."
                ";

        if($parent_id == null){
            $q.="
                order by updated asc
                limit ".$this->limit_count."
            ";
        }


       
		$r=$db->query($q)->result(false);

		foreach($r as $row){
            $updated_date = $row['updated'];
            if(strlen($updated_date)>0){
                if($this->last_update_date== ''){
                    $this->last_update_date = $updated_date;
                }
                else{
                    if($this->last_update_date<$updated_date)
                        $this->last_update_date = $updated_date;
                }
            }
            //Resto_synchronize_Controller::$watcher[count(Resto_synchronize_Controller::$watcher)-1]['new_updated'] = Resto_synchronize_Controller::$last_update_date;

			switch($row['query_command']) {
				case "insert":
					$data[]=$this->insert_statements($table,$row);
				break;
				case "update":
					$data[]=$this->update_statements($table,$row);
				break;
			}
			if($this->is_after_sync_delete($table)){
				$this->delete_records[]=$this->delete_statements($row[$table.'_id'],$table);
			}
			foreach($child_table as $row_child_table){
				$data[]=$this->delete_statements($row[$table.'_id'],$table,$row_child_table);
				$data=array_merge($data,synchronize_ajax::generate_query_statement($row_child_table,$last_synchronize_date,$row[$table.'_id'],$table));
			}
		}

		return $data;
	}
	
	private function is_after_sync_delete($table_name) {
		return in_array($table_name,$this->after_sync_delete);
	}
	
	private function execute($method) {
		
		$process_url=ccfg::get('server_synchronize_url').'execute';
		//todo get query statements for data
		$data =array();
		if(isset($this->data[$method])) {
			if(isset($this->data[$method]['table'])) {
				$tables = $this->data[$method]['table'];
				foreach($tables as $table=>$v) {
					$data = array_merge($data,$this->query_statements($table));
				}
			}	
		}
		
		
		$postdata = array(
			"session_id"=>$this->session_id,
			"statements"=>$data,
		);
		
		$jsondata = json_encode($postdata);
		$compressed   = gzdeflate($jsondata);
		$result = $this->http_post($process_url,$compressed);
		$data = cjson::decode($result);
		
		$error=0;
		$error_message = "";
		if($error==0) {
			if(!is_array($data)) {
				$error++;
				$error_message =  "Error on server execute [21]";
			}
		}
		if($error==0) {
			if(!isset($data['result'])) {
				$error++;
				$error_message = "Error on server execute [22]";
			}
		}
		
		if($error==0) {
			if($data['result']<=0){
				$error++;
				$error_message=$data['message']."[2]";
			}
		}
		if($error>0) {
			$this->add_error($error_message);
		}
		return $error;
	}
	
	private function finalize($method) {
		$app = CApp::instance();
		$org = $app->org();
		$org_id=$org->org_id;
		$store_id = ccfg::get("store_id");
		$finalize_url=ccfg::get('server_synchronize_url').'finalize';
		$postdata = array(
			"session_id"=>$this->session_id,
		);
		$jsondata = json_encode($postdata);
		$result = $this->http_post($finalize_url,$jsondata);
		$data = cjson::decode($result);
		
		$error=0;
		$error_message = "";
		if($error==0) {
			if(!is_array($data)) {
				$error++;
				$error_message =  "Error on server finalize [31]";
			}
		}
		if($error==0) {
			if(!isset($data['result'])) {
				$error++;
				$error_message = "Error on server finalize [32]";
			}
		}
		
		if($error==0) {
			if($data['result']<=0){
				$error++;
				$error_message=$data['message']."[3]";
			}
		}
		if($error==0){
			//do delete records
			$db = CDatabase::instance();
			
			foreach($this->delete_records as $row_delete){
				$db->query($row_delete);
			}
			//only update files when update last updated flag is true

			if($this->last_update_date == ''){
				
				cdata::set($method,date('Y-m-d H:i:s'),'client_last_synchronize/'.$org_id.'/'.$store_id);
			} else {
				
				cdata::set($method,$this->last_update_date,'client_last_synchronize/'.$org_id.'/'.$store_id);
			}
			
		}
		if($error>0) {
			$this->add_error($error_message);
		}
		return $error;
	}
	
	
	private function http_post($url,$postdata) {
		$result=CCurl::factory($url)->set_raw_post($postdata)->exec()->response();;
		return $result;
	}
}