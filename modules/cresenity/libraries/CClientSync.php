<?php

class CClientSync {

	private $data;
	
	private $errors;
	private $session_id;
	private $client_last_update;
	private $limit_count;
	private $synced_data = array();
	private $org_id = "";
	private $store_id = "";
	
	private function __construct($org_id,$store_id,$data) {
		$this->data =$data;
		$this->org_id =$org_id;
		$this->store_id =$store_id;
		$this->errors = array();
		$this->session_id = "";
		
		$this->limit_count = 20;
		$synchronize_record_count = ccfg::get('synchronize_record_count');
		if(strlen($synchronize_record_count)>0) {
			$this->limit_count = $synchronize_record_count;
		}
		$this->parse_data();
	}
	
	public static function factory($org_id,$store_id,$data) {
		return new CClientSync($org_id,$store_id,$data);
	}
	
	public function parse_data() {
		foreach($this->data as $table_name=>$data_table) {
			
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
	
	public function get_child_table($table) {
		$childs = array();
		foreach($this->data as $table_name=>$data_table) {
			if($table_name==$table) {
				if(isset($data_table["child"])) {
					$childs_data = $data_table["child"];
					foreach($childs_data as $k=>$v) {
						$childs[]=$k;
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
	
	public function synchronize($table) {
		//step : initialize,execute,finalize
		if(!$this->is_error()) {
			$this->initialize($table);
		}
		
		if(!$this->is_error()) {
			$this->execute($table);
		}
		
		if(!$this->is_error()) {
			$this->finalize($table);
		}
		
		return !$this->is_error();
	}
	
			
	private function initialize($method) {
		
		$initialize_url=ccfg::get('server_synchronize_url').'initialize';
		$postdata = array(
			"org_id"=>$this->org_id,
			"store_id"=>$this->store_id,
			"method"=>$method,
		);
		$jsondata = json_encode($postdata);
		$result = $this->http_post($initialize_url,$jsondata);
		$data = cjson::decode($result);
		$this->synced_data = array();
		$error=0;
		$error_message = "";
		if($error==0) {
			if(!is_array($data)) {
				$error++;
				$error_message =  "Error on server initialize [11]".$data;
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
				cdata::set($method,$data['forced_datetime'],'client_last_synchronize/'.$this->org_id.'/'.$this->store_id);
			}

		}
		if($error==0) {
			$this->client_last_update = cdata::get($method,'client_last_synchronize/'.$this->org_id.'/'.$this->store_id);
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
	
	
	private function generate_statements($table,$row) {
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
		$query.="replace into ".$db->escape_table($table)."(".substr($str_insert_into, 0, strlen($str_insert_into)-1).")values(".substr($str_value, 0, strlen($str_value)-1).");";
		return $query;
	}
	private function query_statements($table) {
		$db = CDatabase::instance();
		$statements=array();
		
		$q="
			select
				*
			from
				".$db->escape_table($table)."
			where
				org_id=".$db->escape($this->org_id)."
				and store_id=".$db->escape($this->store_id)."
				and sync_status=0
		";
		switch($table) {
			case "resto_transaction":
				$q.="
					and is_checkout>0
				";
			break;
		}

		$q.="
                order by updated asc
                limit ".$this->limit_count."
		";
   
		
       
		$r=$db->query($q)->result(false);
		$childs_table=$this->get_child_table($table);
		foreach($r as $row){
			//$statements[] = $this->generate_statements($table,$row);
			$statements[] = array(
				'table'=>$table,
				'data'=>$row,
				'where'=>array('org_id'=>$this->org_id,'store_id'=>$this->store_id,$table."_id"=>$row[$table.'_id']),
				'method'=>'replace'
			);
			foreach($childs_table as $child_table){
				//$statements[] = 'delete from '.$db->escape_table($child_table)." where ".$db->escape_column($table."_id")."=".$db->escape($row[$table.'_id']).";";
				$statements[] = array(
					'table'=>$child_table,
					'data'=>array(),
					'where'=>array('org_id'=>$this->org_id,'store_id'=>$this->store_id,$table."_id"=>$row[$table.'_id']),
					'method'=>'delete'
				);
				$q2="select * from ".$db->escape_table($child_table)." where ".$db->escape_column($table."_id")."=".$db->escape($row[$table.'_id']).";";
				$r2 = $db->query($q2)->result(false);
				foreach($r2 as $row2) {
					//$statements[] = $this->generate_statements($child_table,$row2);
					$statements[] = array(
						'table'=>$child_table,
						'data'=>$row2,
						'where'=>array('org_id'=>$this->org_id,'store_id'=>$this->store_id,$child_table."_id"=>$row2[$child_table.'_id']),
						'method'=>'replace'
					);
				}
			}
			
			$this->synced_data[] = array("table"=>$table,"id"=>$row[$table.'_id']);
		}

		return $statements;
	}
	

	
	private function execute($method) {
		$error=0;
		$error_message = "";
		$process_url=ccfg::get('server_synchronize_url').'execute';
		//todo get query statements for data
		$data =array();
		try {
			if(isset($this->data[$method])) {
				$data = array_merge($data,$this->query_statements($method));
			}
		} catch(Exception $ex) {
			$error++;
			$error_message =  $ex->getMessage();
		}
		$data = cphp::save_value($data);
		$postdata = array(
			"session_id"=>$this->session_id,
			"statements"=>$data,
		);
		
		$jsondata = json_encode($postdata);
		
		$jsondata   = gzdeflate($jsondata);
		$result = $this->http_post($process_url,$jsondata,'application/x-gzip');
		$data = cjson::decode($result);
		
		
		if($error==0) {
			if(!is_array($data)) {
				$error++;
				$error_message =  "Error on server execute [21]".$data;
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
				$error_message =  "Error on server finalize [31]".$data;
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
			
			
			//only update files when update last updated flag is true

			foreach($this->synced_data as $val) {
				$table = $val['table'];
				$id = $val['id'];
				$q="update ".$db->escape_table($table)." set sync_status = 1 where ".$db->escape_column($table."_id")."=".$db->escape($id);
				$db->query($q);
				if(ccfg::get('have_delete_resto_transaction_after_synchronize')) {
					switch($table) {
						
						case "resto_transaction":
						case "resto_payment":
						
							$q="delete from ".$db->escape_table($table)." where ".$db->escape_column($table."_id")."=".$db->escape($id);
							$r = $db->query($q);
							
							$childs_table=$this->get_child_table($table);
							
							foreach($childs_table as $child_table){
								$q = 'delete from '.$db->escape_table($child_table)." where ".$db->escape_column($table."_id")."=".$db->escape($id).";";
								$r = $db->query($q);
							}
							
							
						break;
					}
					
				}
			}
				
			cdata::set($method,date('Y-m-d H:i:s'),'client_last_synchronize/'.$this->org_id.'/'.$this->store_id);
			
		}
		if($error>0) {
			$this->add_error($error_message);
		}
		return $error;
	}
	
	
	private function http_post($url,$postdata,$content_type="text/plain") {
		$result=CCurl::factory($url)->set_raw_post($postdata,$content_type)->exec()->response();;
		$db = CDatabase::instance();
		$app = CApp::instance();
		$session_id = csess::session_id();
		$ip_address = crequest::remote_address();
		$method='post';
		$app_id = $app->app_id();
		$request_type = "client_sync";
		$data = array(
			
			'request_date'=>date("Y-m-d H:i:s"),
			'app_id'=>$this->org_id,
			'org_id'=>$this->org_id,
			'store_id'=>$this->store_id,
			'session_id'=>$session_id,
			'request_type'=>$request_type,
			'remote_addr'=>$ip_address,
			'method'=>$method,
			'request'=>$postdata,
			'response'=>$result,
			'url'=>$url,
			
			
		);
		
		
		$db->insert('log_http_request',$data);
		return $result;
	}
}