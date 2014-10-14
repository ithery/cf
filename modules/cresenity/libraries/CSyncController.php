<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Cresenity PHP Library.
 * @author     Hery Kurniawan
 */

class CSyncController extends CController {
	
	public $api_key = "";
	public $data = "";
	public function __construct() {
		parent::__construct();
		//get request post
		$post_data = "";
		$putdata = fopen( "php://input" , "rb" ); 
		while(!feof( $putdata )) 
			$post_data .=fread($putdata, 4096 ); 
		fclose($putdata); 
		
		
		$error=0;
		$error_message = "";
		$json = cjson::decode($post_data);
		//check for api data
		if($error==0) {
			if(!isset($json["api_key"])) {
				$error++;
				$error_message = "Invalid API Request Format, missing API Key";
			}
		}
		if($error==0) {
			if(!isset($json["data"])) {
				$error++;
				$error_message = "Invalid API Request Format, missing Data";
			}
		}
		if($error>0) {
			$error_json = csync::error_response($error_message);
			echo $error_json;
			die();
		}
		$this->api_key = $json["api_key"];
		$this->data = $json["data"];
		
	}
	
}