<?php
class clogger {
	public static function log($filename,$type,$message) {
		$date = date("Y-m-d H:i:s");
		$str = $date." ".$type." ".$message."\r\n";
		$filename = DOCROOT."/log/".date("Ymd")."_".$filename;
		$fh = @fopen($filename, 'a+');
		fwrite($fh,$str);
		@fclose($fh); 	
	}

}

?>