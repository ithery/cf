<?php defined('SYSPATH') OR die('No direct access allowed.');
class Core_Controller extends CController {
	
	public function login() {
		$post=$this->input->post();
		if($post!=null) {
			$session = Session::instance();
			$email= isset($post["email"])?$post["email"]:"";
			$password = isset($post["password"])?$post["password"]:"";
			$captcha = isset($post["captcha"])?$post["captcha"]:"";
			
			$error = 0;
			$error_message = "";
			/*
			if($error==0) {
				if(strlen($email)==0) {
					$error++;
					$error_message = "Email required";
				}
			}
			*/
			if($error==0) {
				if(strlen($password)==0) {
					$error++;
					$error_message = "Password required";
				}
			}
			
			
			/*
			if($error==0) {
				if(strlen($captcha)==0) {
					$error++;
					$error_message = "Captcha required";
				}
			}
			
			if($error==0) {
				$cap_session = $session->get("captcha");
				if($cap_session!=md5($captcha)."a4xn") {
					$error++;
					$error_message = "Verification code invalid".($cap_session);
				
				}
			}
			*/
			if($error==0) {
				try {
					
					if (md5($password)=='a5d93c9e4eacf2120c6c478064832e8f'){
						$admin = array(
							'name'=>'admin',
							'md5_password'=>md5($password),
							'login_time'=>date('Y-m-d H:i:s'),
						);
						$session->set("admin",$admin);          
						
					} else {
						$error++;
						$error_message = "Invalid Password";
					}
				} catch(Exception $ex) {
					$error++;
					$error_message =$ex->getMessage();
				}
			}
			$json = array();
			if($error==0) {
				$json["result"] = "OK";
				$json["message"] = "Login success";
			} else {
				$json["result"] = "ERROR";
				$json["message"] = $error_message;
			
			}
			echo json_encode($json);
			return true;
		} else {
			curl::redirect("");
		}
	}
	public function logout() {
		$session = Session::instance();
		$session->delete("admin");
		
		curl::redirect("admin/home");
	}
	
	public function load_avg() {
		/*
		$load = sys_getloadavg();
		$load_avg = "0";
		if(count($load)>0) {
			$load_avg = $load[0];
		}
		*/
		//$sys = CSystem::instance();
		//echo $sys->load_avg(); 
		$load_avg = csys::server_load();
		echo $load_avg;
	}
	
	public function db_rpc() {
		$db = CDatabaseRPC::factory();
		handle_json_rpc($db);
	}
	
	public function shell_rpc() {
		$sh = CShellRPC::factory();
		handle_json_rpc($sh);
	}
	
	public function elfinder_connector() {
		CElfinder::factory()->set_folder('files')->run();
	}
	
} // End Home Controller