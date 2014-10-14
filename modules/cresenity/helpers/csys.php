<?php defined('SYSPATH') OR die('No direct access allowed.');
class csys {

	function is_running($togrep) {
		
		if (csysfunc::execute('ps', '-ef | grep "'.$togrep.'" | grep -v grep', $bufr)) {
			if(strpos($bufr,$togrep)!==false) {
				return true;
			}
		}
		return false;
	}

	function memory_info() {
		$keys = array(
			'memory_total',
			'memory_free',
			'memory_cache',
			'memory_buffer',
			
		);
		$r = array();
		//init r
		foreach($keys as $k) $r[$k]=0;
		
		if (stristr(PHP_OS, 'Linux')) {
			$system_file = '/proc/meminfo';
			$buffer = '';
			if (file_exists($system_file)) {
				$buffer = file_get_contents($system_file);
			}
			$bufe = preg_split("/\n/", $buffer, -1, PREG_SPLIT_NO_EMPTY);
			foreach ($bufe as $buf) {
                if (preg_match('/^MemTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $r['memory_total'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^MemFree:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $r['memory_free'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^Cached:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $r['memory_cache'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^Buffers:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $r['memory_buffer'] = $ar_buf[1] * 1024;
                }
            }
		}
		return $r;
	}
	function memory_usage() {
		return memory_get_usage(false);
	}
	
	function real_memory_usage() {
		return memory_get_usage(true);
	}
	
	function server_load() {
    
        if (stristr(PHP_OS, 'win')) {
        
            $wmi = new COM("Winmgmts://");
            $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
            
            $cpu_num = 0;
            $load_total = 0;
            
            foreach($server as $cpu){
                $cpu_num++;
                $load_total += $cpu->loadpercentage;
            }
            
            $load = round($load_total/$cpu_num);
            
        } else {
        
            $loadavg_file = '/proc/loadavg';
			$sys_load = array(0,0,0);
			if (file_exists($loadavg_file)) {
				$sys_load = explode(chr(32),file_get_contents($loadavg_file));
			} 
			//$sys_load = sys_getloadavg();
    		
			$load = $sys_load[0];
        
        }
        
        return $load;
    
    }
	public static function os() {
		return PHP_OS;
	}
	public static function php_version() {
		  return PHP_VERSION; 
	}
	public static function func_enabled($func) {
		
		$disabled = explode(',', ini_get('disable_functions'));
		foreach ($disabled as $disableFunction) {
			$is_disabled[] = trim($disableFunction);
		}
		if (in_array($func,$is_disabled)) {
			$it_is_disabled["m"] = $func.'() has been disabled for security reasons in php.ini';
			$it_is_disabled["s"] = 0;
		} else {
			$it_is_disabled["m"] = $func.'() is allow to use';
			$it_is_disabled["s"] = 1;
		}
		return $it_is_disabled["s"]==1;

	}
	public static function mysql_version() {
		
		$output = "";
		if(csys::func_enabled("shell_exec")) {
			$output = shell_exec('mysql -V'); 
			preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
			if(isset($version)&&count($version)>0) {
				$output = $version[0];
			}
		}
		/*
		if($output=="") {
			try {
				$output= mysql_get_server_info();
			} catch(Exception $ex) {
			}
		}
		*/
		return $output; 
	}
	public static function apache_version() {
		$version="";
		
		if(isset($_SERVER['SERVER_SOFTWARE'])) {
			//$ver = explode("/",$_SERVER['SERVER_SOFTWARE']);
			
			$ver = preg_split("[/ ]",$_SERVER['SERVER_SOFTWARE']);
			
			if(count($ver)>1) {
				$version.=$ver[1];
			}
			if(count($ver)>2) {
				$version.=$ver[2];
			}
			
			
		} 
		if(strlen($version)==0){
			if(function_exists("apache_get_version")) {
				$version = apache_get_version();
			}
		
		}
		if(strlen($version)==0){
			$version = $_SERVER['SERVER_SOFTWARE'];
		}
		return $version; 
	}
	public static function hostname() {
		if (stristr(PHP_OS, 'win')) {
			return php_uname("n");
		} else {
			if(csys::func_enabled("exec")) {
				return exec('hostname -f');
			}
			
		}
		return "";
	}
	public static function ip_address() {
		if(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}
		return "";
	}
	public static function external_ip_address() {
		$extip = "0.0.0.0";
		try {
			//$extip = file_get_contents('http://phihag.de/ip/');
		} catch(Exception $ex) {
		
		}
		return $extip;
	}
	public static function u_name() {
		return php_uname('a');
	}
}