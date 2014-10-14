<?php

class CFTP {
	protected $ftp;
	protected $state;
	
	protected function __construct($url = NULL) {
		if (!extension_loaded('ftp')) {
			throw new Exception("PHP extension FTP is not loaded.");
		}
		$this->last_result = null;
		$this->state=array();
		if ($url!=null) {
			$parts = parse_url($url);
			$this->connect($parts['host'], empty($parts['port']) ? NULL : (int) $parts['port']);
			$this->login($parts['user'], $parts['pass']);
			$this->pasv(TRUE);
			if (isset($parts['path'])) {
				$this->chdir($parts['path']);
			}
		}
	}
	public function connect($host,$port=null,$ssl=false) {
		if($ssl) {
			$this->ftp = ftp_connect($host,$port);
		} else {
			$this->ftp = ftp_connect($host,$port);
		}
		$this->state['connect']=array($host,$port,$ssl);
		return $this->ftp;
	}
	
	public function login($username,$password) {
		$result =  ftp_login($this->ftp,$username,$password);
		$this->state['connect']=array($username,$password);
	}
	public function __destruct() {
		return @ftp_close($this->ftp);
	}
	
	public function factory($url = NULL) {
		return new CFTP($url);
	}
	
	//get current directory
	//return string
	public function pwd() {
		return ftp_pwd($this->ftp);
	}
	
	//change directory
	//return bool
	public function chdir($dir) {
		$result = ftp_chdir($this->ftp,$dir);
		$this->state['chdir'] = array($this->pwd());
		return $result;
	}
	
	public function cdup() {
		$result = ftp_cdup($this->ftp);
		$this->state['chdir'] = array($this->pwd());
		return $result;
	}
	
	//download file
	//return bool
	public function get($local_file,$server_file) {
		return ftp_get($this->ftp, $local_file, $server_file, FTP_BINARY);
	}
	//upload file
	//return bool
	public function put($server_file,$local_file) {
		return ftp_put($this->ftp, $server_file, $local_file, FTP_BINARY);
	}
	
	//return last modified date of remote file
	public function mdtm($remote_file) {
		return ftp_mdtm($this->ftp,$remote_file);
	}
	
	//return bool
	public function mkdir($dir) {
		return ftp_mkdir($this->ftp,$dir);
	}
	
	//get file list of remote directory, not defined param dir return all files on currenct directory
	//return array
	public function nlist($dir=".") {
		return ftp_nlist($this->ftp,$dir);
	}
	
	
	public function file_exists($file) {
		return is_array($this->nlist($file));
	}
	
	public function is_dir($dir) {
		$current = $this->pwd();
		$error++;
		try {
			$this->chdir($dir);
		} catch (Exception $e) {
			$error++;
		}
		$this->chdir($current);
		return $error==0;
	}
	
	public function mkdir_recursive($dir) {
		$parts = explode('/', $dir);
		$path = '';
		while (!empty($parts)) {
			$path .= array_shift($parts);
			try {
				if ($path !== '') $this->mkdir($path);
			} catch (Exception $e) {
				if (!$this->is_dir($path)) {
					throw new Exception("Cannot create directory '$path'.");
				}
			}
			$path .= '/';
		}
	}
	
	public function rmdir($dir) {
		return ftp_rmdir($this->ftp,$dir);
	}
	
	public function delete($file) {
		return ftp_delete($this->ftp,$file);
	}
	
	public function delete_recursive($path){
		if (!$this->delete($path)) {
			foreach ((array) $this->nlist($path) as $file) {
				if ($file !== '.' && $file !== '..') {
					$this->delete_recursive(strpos($file, '/') === FALSE ? "$path/$file" : $file);
				}
			}
			$this->rmdir($path);
		}
	}

	public function reconnect() {
		
		@ftp_close($this->ftp); // intentionally @
		foreach ($this->state as $name => $args) {
			call_user_func_array(array($this, $name), $args);
		}
	}

	public function pasv($bool) {
		ftp_pasv($this->ftp,$bool);
	}
	
	
}