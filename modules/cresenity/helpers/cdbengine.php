<?php
class cdbengine {
	
	public function backup() {
		$backup_type = "zip";
		$backup_dir = DOCROOT."backup".DIRECTORY_SEPARATOR;
		if(!is_dir($backup_dir)) {
			mkdir($backup_dir);
		}
		$filename= "backup_".date('YmdHis').".sql"; 
		switch($backup_type) {
			case "zip":
				$filename= "backup_".date('YmdHis').".zip"; 
			break;
			case "sql":
			default:
			break;
		}
		$app = CApp::instance();
		$user = $app->user();
		
		$fullfilename = $backup_dir.$filename;
		$error = cdbutils::backup($fullfilename,$backup_type);
		clog::backup($user->user_id,$filename,$backup_dir);
		return $error;
	}
	
	public function restore($log_backup_id) {
		set_time_limit(300);
		$backup_dir = DOCROOT."backup".DIRECTORY_SEPARATOR;
		$error = 0;
		$error_message = "";
		$db = CDatabase::instance();
		$q = "select filename from log_backup where log_backup_id=".$db->escape($log_backup_id);
		$r=$db->query($q);
		$filename = "";
		
		if($r->count()>0) {
			$filename = $r[0]->filename;
		}
		$backupfile = $backup_dir.$filename;
		try {
			cdbutils::restore($backupfile);
		}catch(Exception $ex) {
			$error++;
			$error_message = $ex->getMessage();
		}
		
	}
	
	
	
	
}