<?php

class CJDBTable {
	
	private $data = array();
	private $file = "";
	public function __construct($file,$create_not_exists=true) {
		$this->file = $file;
		if($create_not_exists) {
			if(!file_exists($file)) {
				file_put_contents($file,"[]");
			}
		}
		$this->data = cjson::decode(file_get_contents($file));
		if(!is_array($this->data)) {
			trigger_error('Data error when retrieving data from '.$file);
		}
	}
	
	public static function factory($file) {
		return new CJDBTable($file);
	}
	
	public function data_object() {
		return json_decode(json_encode($this->data));
	}
	public function data($object=false) {
		if($object) return $this->data_object();
		return $this->data;
	}
	public function insert($data) {
		$this->data[]=$data;
		return $this;
	}
	
	public function update($data,$where) {
		
		
		foreach($this->data as &$d) {
			$pass=0;
			
			
			if(is_array($where)) {
				$pass=1;
				foreach($where as $k=>$v) {
					if(isset($d[$k])&&$d[$k]==$v) {
						$pass&=1;
					} else {
						$pass&=0;
					
					}
				}
			} else {
				$pass=1;
			}
			
			if($pass==1) {
				foreach($data as $k=>$v) {
					if(isset($d[$k])) {
						$d[$k]=$v;
					}
				}
			}
		}
		return $this;
	}
	public function save() {
		$json = cjson::encode($this->data);
		return file_put_contents($this->file,$json);
	}
	
	
	public function get($where=null) {
		$result = array();
		
		foreach($this->data as $d) {
			$pass=0;
			if(is_array($where)) {
				$pass=1;
				foreach($where as $k=>$v) {
					if(isset($d[$k])&&$d[$k]==$v) {
						$pass&=1;
					} else {
						$pass&=0;
					
					}
				}
			} else {
				$pass=1;
			}
			if($pass==1) $result[]=$d;
		}
		return CJDBResult::factory($result);
	}
	
	public function select($column=null,$where=null) {
		$result = array();
		
		foreach($this->data as $d) {
			$pass=0;
			if(is_array($where)) {
				$pass=1;
				foreach($where as $k=>$v) {
					if(isset($d[$k])&&$d[$k]==$v) {
						$pass&=1;
					} else {
						$pass&=0;
					
					}
				}
			} else {
				$pass=1;
			}
			if($pass==1) {
				if(is_array($column)) {
					$dd = array();
					foreach($column as $col) {
						if(!isset($d[$col])) {
							throw new Exception('Column '.$col.' doesn\'t exists');
						}
						$dd[$col] = $k[$col];
					}
					$result[]=$dd;
				} else {
					$result[]=$d;
				}
			}
		}
		return CJDBResult::factory($result);
	}
	public function get_list($key,$value,$where=null) {
		$result = array();
		
		foreach($this->data as $d) {
			if(!isset($d[$key])) {
				throw new Exception('Column '.$col.' doesn\'t exists');
			}
			if(!isset($d[$value])) {
				throw new Exception('Column '.$col.' doesn\'t exists');
			}

			$pass=0;
			if(is_array($where)) {
				$pass=1;
				foreach($where as $k=>$v) {
					if(isset($d[$k])&&$d[$k]==$v) {
						$pass&=1;
					} else {
						$pass&=0;
					
					}
				}
			} else {
				$pass=1;
			}
			if($pass==1) {
				$result[$d[$key]]=$d[$value];
			}
		}
		return $result;
	}
	
	public function delete($where=null) {
		$result = array();
		
		foreach($this->data as $d) {
			$pass=0;
			if(is_array($where)) {
				$pass=1;
				foreach($where as $k=>$v) {
					if(isset($d[$k])&&$d[$k]==$v) {
						$pass&=1;
					} else {
						$pass&=0;
					
					}
				}
			} else {
				$pass=1;
			}
			if($pass==0) $result[]=$d;
		}
		$this->data = $result;
		return $this;
	}
}