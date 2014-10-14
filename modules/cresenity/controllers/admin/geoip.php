<?php defined('SYSPATH') OR die('No direct access allowed.');
class Geoip_Controller extends CController {
	
	public function install() {
		$db = CDatabase::instance();
		$error=0;
		$error_message = "";
		set_time_limit(10000);
		if(isset($_GET["install"])&&$_GET["install"]=="true") {
			if($error==0) {
				//we create table geoip
				if(!cdbutils::table_exists('geoip_country')) {
					$q = "
						create table geoip_country(
							start_ip char(15) NOT NULL,
							end_ip	char(15) NOT NULL,
							start INT UNSIGNED NOT NULL,
							end INT UNSIGNED NOT NULL,
							cc CHAR(2) NOT NULL,
							cn varchar(255) NOT NULL
						)Engine=InnoDB,charset=utf8;
					";
					$r = $db->query($q);
					
					
				}
			}
			if($error==0) {
				if(!cdbutils::table_exists('geoip_country')) {
					$error++;
					$error_message = "Error, fail to create table geoip_country";
				}
			}
			
			$csv  = "";
			if($error==0) {
				//we assume there are country table on this database
				//we locate our csv database
				$csv = DOCROOT."data".DIRECTORY_SEPARATOR."core".DIRECTORY_SEPARATOR."geoip_country.csv";
				if(!file_exists($csv)) {
					$error++;
					$error_message = "Error, fail to locate geoip_country csv database";
				}
				
			}
			
			if($error==0) {
				$csv_text = file_get_contents($csv);
				
				$csv_line = explode("\n",$csv_text);
				$total_line = count($csv_line);
				$i=0;
				$db->query("delete from geoip_country");
				
				foreach($csv_line as $line) {
					$i++;
					
					$fields = explode('","',$line);
					
					
					$data = array(
						"start_ip"=>trim($fields[0],'"'),
						"end_ip"=>trim($fields[1],'"'),
						"start"=>trim($fields[2],'"'),
						"end"=>trim($fields[3],'"'),
						"cc"=>trim($fields[4],'"'),
						"cn"=>trim($fields[5],'"'),
					);
					$db->insert("geoip_country",$data);
					if($i%20==0||$i==$total_line) {
						cprogress::set_percent(round($i*100/$total_line,2),''.$i."/".$total_line." Completed");
						if(cprogress::cancelled()) {
							$error++;
							$error_message = "Error, User cancelled the process";
						}
					}
				}
			}
			
		}
	
		if($error==0) {
			echo "Success install GEO IP";
		} else {
			echo $error_message;
		}
	}
	
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("Geo IP"));

		//check table geoip
		
		
		$div=$app->add_div()->add_class('well');
		$installed = true;
		if(!cdbutils::table_exists('geoip_country')) {
			$installed = false;
			
			
		}
		if($installed) {
			$div->add('<h3>GEO IP</h3><br/><br/>');
		} else {
			$div->add('<h3>GEO IP Not installed</h3><br/><br/>');
		}
		$f = $div->add_form();
		$action=$f->add_action_list()->set_style('btn-group')->add_action();
		if($installed) {
			$action->set_label('Reinstall GEO IP')->set_submit(true);
		} else {
			$action->set_label('Install GEO IP')->set_submit(true);
		}
		$f->set_ajax_submit(true)->set_ajax_redirect(true)->set_action(curl::base()."admin/geoip/install?install=true");
		//$f->set_ajax_upload_progress(true);
		$f->set_ajax_process_progress(true);
		$f->set_ajax_process_progress_cancel(true);
		$f->set_ajax_redirect_url(curl::base()."admin/geoip");
		$f->add_control('is_ajax','hidden')->set_value('1');
		echo $app->render();
	}

	
	
	
	
	
} // End Home Controller