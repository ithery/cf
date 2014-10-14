<?php defined('SYSPATH') or die('No direct access allowed.');

class CExcelOdbc {
	private $odbc = null;
	private $filename = "";
	private $connection_string = "";
	public function __construct($filename) {
		$this->filename = $filename;
		$connection_string="Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq=".$filename.";ReadOnly=False;DefaultDir=".dirname($filename)."";
		$this->odbc = odbc_connect( $connection_string, '', '');
		$this->connection_string = $connection_string;
		//odbc_connect( $connection_string, '', '');
		//$this->db = new CDatabase("odbc://".$connection_string);
		//$this->db->query("insert into [dummy$]([TERRITORY]) values ('test')"); 
		
	}
	
	public function __destruct() {
		@odbc_close($this->odbc);
	}
	
	public static function factory($filename) {
		return new CExcelOdbc($filename);
	}
	
	public function query($sql) {
		odbc_exec($this->odbc,$sql);
	}
	public function filename() {
		return $this->filename;
	}
}