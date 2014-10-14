<?php defined('SYSPATH') OR die('No direct access allowed.');
class Ctest_Controller extends CController {
	
	public function index() {
		// The message
		//$message = "Line 1\r\nLine 2\r\nLine 3";

		// In case any of our lines are larger than 70 characters, we should use wordwrap()
		//$message = wordwrap($message, 70, "\r\n");

		// Send
		//mail('hery.cresenity@gmail.com', 'My Subject', $message);
		
		//cmail::register(3);
		
		$q = "show create table customer";
		$str = cdbutils::get_value($q);
		die($str);
		
		$a = array('a'=>'dd','b'=>'');
		$b = array('b'=>'test');
		$c = array_merge($a,$b);
		print_r($c);
		die();
		
		if (!preg_match('/^[0-9-]*$/', '031-222')) {
			echo 'not telp';
		} else {
			echo 'telp';
		}
	}
	
	public function currency() {
		$app = CApp::instance();
		$widget = $app->add_widget();
		$form = $widget->add_form();
		$form->add_field()->set_label('Currency')->add_control('idr','currency');
		echo $app->render();
	}

	function config() {
		$config = CConfig::instance('testing.testing2.abc');
		$cfg = $config->config();
		print_r($cfg);
		$cfg['alv1_string']='level1_string';
		$cfg['alv1_array']=array(
			'alv2_string'=>'level2_string',
			'alv2_null'=>null,
			'alv2_array'=>array(
				'alv3_string'=>'level3_string',
				'alv3_double'=>5.23123,
				'alv3_bool_false'=>false,
				
			),
			'alv2_int'=>5,
			'alv2_double'=>5.2,
			
			
		);
		$cfg['alv1_empty_array']=array();
		$cfg['alvl1_array']['alv2_array']['alv3_array']=array(
			'alv4_bool_true'=>true,
			'alv4_string_array'=>array('hallo1','hallo2','hallo3'),
			
		);
		$cfg['alv1_int_array']=array(4,3,4,2,1);
		$config->save($cfg);
		
	}
	function validate_phone($phone) {
		return preg_match('/^[0-9-]*$/', $phone);
	}
	
	function zip_extract() {
		$to = DOCROOT."temp/";
		$zipfile = DOCROOT."download/client/1.0.0.zip";
		$zip = CZip::factory($zipfile)->extract($to);
	}
	
	function pop3() {
		$app = CApp::instance();
		$app->title('POP3 test');
		$user = 'herycresenity@gmail.com';
		$pass = 'Project2012';
		
		$pop3 = CPOP3::factory()->set_user($user)->set_password($pass)->set_host('gmail')->login();
		$last_message = $pop3->get_last_message();
		
		
		echo $app->render();
	}
	function ckeditor() {
		$app = CApp::instance();
		$app->title("Test CK Editor");
		
		$w = $app->add_widget();
		$f = $w->add_form();
		$f->add_field()->set_label('Editor')->add_control('editor','ckeditor')->toolbar_full();
		$f->add_action_list()->add_action()->set_submit(true)->set_label('Submit')->set_icon('ok');
		echo $app->render();
	}
	
	function set_apply_data_table(){
		$app = CApp::instance();
		$app->title("test set_apply_data_table");
		
		$w = $app->add_widget();
                $f = $w->add_form();
                $f->set_apply_data_table(true);
		
	}
	
	function progress() {
		$post = $this->input->post();
		if($post!=null) {
			//do progress
			$error = 0;
			$error_message = '';
			$max = 50;
			for($i=0;$i<=$max;$i++) {
				cprogress::set_percent($i*100/$max,''.$i."/".$max." Completed");
				
				if(cprogress::cancelled()) {
					$error++;
					$error_message = "Error, User cancelled the process";
				}
				
				if($error>0) {
					break;
				}
				sleep(1);
			}
			if($error==0) {
				cmsg::add('success','Success progressing');
				die('Success progressing');
			} else {
				cmsg::add('error',$error_message);
				die($error_message);
			}
			
		}
		$app = CApp::instance();
		$app->title("Test Progress Bar");
		
		$w = $app->add_widget();
		$f = $w->add_form();
		$f->add_field()->set_label('File')->add_control('file','file')->add_validation('required');
		$f->add_action_list()->add_action()->set_submit(true)->set_label('Submit')->set_icon('ok');
		$f->set_ajax_submit(true)->set_ajax_redirect(true);
        $f->set_apply_data_table(true);
		//$f->set_enctype('multipart/form-data')->set_ajax_upload_progress(true);
		$f->set_ajax_process_progress(true)->set_ajax_process_progress_cancel(false);
		$f->add_control('is_ajax','hidden')->set_value('1');
		echo $app->render();
	}
	public function excel_odbc() {
		$filename = DOCROOT.'test/'.'AMWeekly_v21a.xls';
		$app = CApp::instance();
		$filename = DOCROOT."test\ClusterSurveyReportTemplate_v2.xls";
		$xlsodbc = CExcelOdbc::factory($filename);
		//$xlsodbc->query("insert into [dummy$]([TERRITORY]) values ('test')");
		$xlsodbc->query("INSERT INTO [dummy$]([Territory] ,[Region] ,[Depo] ,[Cluster] ,[Jumlah_File] ,[SP_Etalase_XL2K] ,[SP_Etalase_XL] ,[SP_Etalase_Simpati] ,[SP_Etalase_AS] ,[SP_Etalase_Mentari] ,[SP_Etalase_IM3] ,[SP_Etalase_AXIS] ,[SP_Etalase_3] ,[SP_Etalase_Esia] ,[SP_Etalase_Flexi] ,[SP_Stock_XL2K] ,[SP_Stock_XL] ,[SP_Stock_Simpati] ,[SP_Stock_AS] ,[SP_Stock_Mentari] ,[SP_Stock_IM3] ,[SP_Stock_AXIS] ,[SP_Stock_3] ,[SP_Stock_Esia] ,[SP_Stock_Flexi] ,[SP_Jual_XL2K] ,[SP_Jual_XL2KBundling] ,[SP_Jual_XL] ,[SP_Jual_Simpati] ,[SP_Jual_AS] ,[SP_Jual_Mentari] ,[SP_Jual_IM3] ,[SP_Jual_AXIS] ,[SP_Jual_3] ,[SP_Jual_Esia] ,[SP_Jual_Flexi] ,[SP_Beli_XL2K] ,[SP_Beli_XL] ,[SP_Beli_Simpati] ,[SP_Beli_AS] ,[SP_Beli_Mentari] ,[SP_Beli_IM3] ,[SP_Beli_AXIS] ,[SP_Beli_3] ,[SP_Beli_Esia] ,[SP_Beli_Flexi] ,[SP_Ranking_XL2K] ,[SP_Ranking_XL] ,[SP_Ranking_Simpati] ,[SP_Ranking_AS] ,[SP_Ranking_Mentari] ,[SP_Ranking_IM3] ,[SP_Ranking_AXIS] ,[SP_Ranking_3] ,[SP_Ranking_Esia] ,[SP_Ranking_Flexi] ,[PV_XL_5000_Display] ,[PV_XL_5000_Stock] ,[PV_XL_5000_Beli] ,[PV_XL_5000_Jual] ,[PV_XL_10000_Display] ,[PV_XL_10000_Stock] ,[PV_XL_10000_Beli] ,[PV_XL_10000_Jual] ,[PV_XL_25000_Display] ,[PV_XL_25000_Stock] ,[PV_XL_25000_Beli] ,[PV_XL_25000_Jual] ,[PV_XL_50000_Display] ,[PV_XL_50000_Stock] ,[PV_XL_50000_Beli] ,[PV_XL_50000_Jual] ,[PV_XL_100000_Display] ,[PV_XL_100000_Stock] ,[PV_XL_100000_Beli] ,[PV_XL_100000_Jual] ,[Dompul_5000_Jual] ,[Dompul_5000_Beli] ,[Dompul_10000_Jual] ,[Dompul_10000_Beli] ,[Dompul_25000_Jual] ,[Dompul_25000_Beli] ,[Dompul_50000_Beli] ,[Dompul_50000_Jual] ,[Dompul_100000_Beli] ,[Dompul_100000_Jual] ,[Dompul_200000_Beli] ,[Dompul_200000_Jual] ,[Diskon_Beli] ,[EPulsa_AS_5000] ,[EPulsa_AS_10000] ,[EPulsa_Simpati_10000] ,[EPulsa_IM3_5000] ,[EPulsa_IM3_10000] ,[EPulsa_Mentari_10000] ,[Kesulitan_Ya] ,[Kesulitan_Tidak] ,[ShopBlind_Ada] ,[ShopBlind_TidakAda] ,[ShopBlindLatestVersion_Ya] ,[ShopBlindLatestVersion_Tidak] ,[Poster_Ada] ,[Poster_TidakAda] ,[PosterLatestVersion_Ya] ,[PosterLatestVersion_Tidak] ,[POP_Ada] ,[POP_TidakAda] ,[POPLatestVersion_Ya] ,[POPLatestVersion_Tidak] ,[POP_INTERNET_Ada] ,[POP_INTERNET_TidakAda] ,[POP_INTERNET_LatestVersion_Ya] ,[POP_INTERNET_LatestVersion_Tidak] ,[POP_BlackBerry_Ada] ,[POP_BlackBerry_TidakAda] ,[POP_BlackBerry_LatestVersion_Ya] ,[POP_BlackBerry_LatestVersion_Tidak] ,[POP_MobileInternet_Ada] ,[POP_MobileInternet_TidakAda] ,[POP_MobileInternet_LatestVersion_Ya] ,[POP_MobileInternet_LatestVersion_Tidak] ,[SP_INTERNET_Etalase_XL] ,[SP_INTERNET_Etalase_Flash] ,[SP_INTERNET_Etalase_Broom] ,[SP_INTERNET_Etalase_3] ,[SP_INTERNET_Etalase_Aha] ,[SP_INTERNET_Stock_XL] ,[SP_INTERNET_Stock_Flash] ,[SP_INTERNET_Stock_Broom] ,[SP_INTERNET_Stock_3] ,[SP_INTERNET_Stock_Aha] ,[SP_INTERNET_Jual_XL] ,[SP_INTERNET_Jual_Flash] ,[SP_INTERNET_Jual_Broom] ,[SP_INTERNET_Jual_3] ,[SP_INTERNET_Jual_Aha] ,[SP_INTERNET_Beli_XL] ,[SP_INTERNET_Beli_Flash] ,[SP_INTERNET_Beli_Broom] ,[SP_INTERNET_Beli_3] ,[SP_INTERNET_Beli_Aha] ,[SP_INTERNET_Ranking_XL] ,[SP_INTERNET_Ranking_Flash] ,[SP_INTERNET_Ranking_Broom] ,[SP_INTERNET_Ranking_3] ,[SP_INTERNET_Ranking_Aha] ) VALUES ( 'CENTRAL JAVA & DIY','CENTRAL JAVA','KUDUS','C1-CJV-KDUS-01',18,20,0,15,15,13,15,7,10,0,0,0,0,0,0,0,0,0,0,0,0,2750,0,0,2550,2050,2050,1900,2300,2000,2000,3000,2000,0,2000,1500,1050,1150,1550,100,1000,1200,1,0,3,2,4,2,6,5,0,0,0,0,0,0,0,0,13000,12000,0,0,0,0,0,0,52000,51000,0,0,0,0,6000,5100,11000,10100,26000,24750,51000,49500,100000,99000,0,0,'0.010000',6000,11000,11000,6000,11000,10100,'0.00000000000000','1.00000000000000','1.00000000000000','0.00000000000000','1.00000000000000','0.00000000000000','1.00000000000000','0.00000000000000','1.00000000000000','0.00000000000000','1.00000000000000','0.00000000000000','1.00000000000000','0.00000000000000','0.88888888888888','0.11111111111111','1.00000000000000','0.00000000000000','0.94444444444444','0.05555555555555','1.00000000000000','0.00000000000000','1.00000000000000','0.00000000000000','1.00000000000000','0.00000000000000',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);");
		
		echo $app->render();
	}
	function export() {
		$app = CApp::instance();
		$org = $app->org();
		$org_id = "";
		if($org!=null) {
			$org_id=$org->org_id;
		}
		$get = $this->input->get();
		$db = CDatabase::instance();
		$actions = $app->add_form()->set_method('get')->add_div()->add_class('row-fluid')->add_action_list();
		$actions->add_action('excelcsv')->set_label('EXCEL CSV')->set_submit(true);
		$app->title("Test Export");
		$table = $app->add_table('roles_table');
		$table->set_numbering(true);
		$table->add_column('role_id')->set_label('ID')->set_visible(false);
		$table->add_column('name')->set_label('Name');
		$table->add_column('created')->set_label('Created')->set_editable(false);
		$table->add_column('createdby')->set_label('Created By')->set_editable(false);
		$q = 'select r.role_id,r.name,r.created,r.createdby,r.updated from roles as r where status>0 and org_id='.$db->escape($org_id).'';
		$table->set_data_from_query($q)->set_key('role_id');
		//$table->add_row_action('edit')->set_label('Edit')->set_icon('pencil');
		//$table->add_row_action('delete')->set_icon('trash');
		$table->set_title('Roles');
		$table->set_ajax(true);
		

		
		$actedit = $table->add_row_action('edit');
		$actedit->set_label("")->set_icon("pencil")->set_link(curl::base()."roles/edit/{param1}");
		$actedit = $table->add_row_action('delete');
		$actedit->set_label("")->set_icon("trash")->set_link(curl::base()."roles/delete/{param1}")->set_confirm(true);

			
		//$table->set_ajax(true);
		//$table->set_editable(true);
		$table->cell_callback_func(array("CTest_Controller","export_cell_callback"));
		if(isset($get["excelcsv"])) {
			$table->export_excelcsv('test.csv');
		}
		echo $app->render();
	}
	
	public static function export_cell_callback($table,$col,$row,$text) {
		$db = CDatabase::instance();
		if($col=="role_id") {
			if($table->is_exported()) return $text;
			return '<a href="'.curl::base().'roles/edit/'.$row["role_id"].'">'.$text.'</a>';
		}
		return $text;
	}
	
	public function error() {
		$app = CApp::instance();
		return 5/0;
	}
	
	public function nestable(){
		$db = CDatabase::instance();
		$app = CApp::instance();
		$tree = CTreeDB::factory('item_type');
		$widget = $app->add_widget()->set_title(clang::__("Nestable"));
        $nestable = $widget->add_nestable();
		
		echo $app->render();
	}
	
	public function print_lx300() {
		
		$printername = 'EPSON LX-300+ /II';
		
		//reset printer
		//$data = chr(27).chr(64);
		
		//FF
		//$data = chr(11); 
		
		
		$data ="".
"BARIS 1
BARIS 2
BARIS 3
BARIS 4

".chr(12)."BARIS 31
BARIS 32
BARIS 33
BARIS 34
BARIS 35
BARIS 36
BARIS 37
BARIS 38
".chr(12)."
";


	
		
		//$data = chr(27).chr(67).chr(100);
		//FF
		//$data = chr(12); 
		
		//reverse FF
		//$data = chr(27).chr(106).chr(100);
		
		//print color
		//black=0, magenta=1, cyan=2, violet=3, yellow=4, red=5, green=6
		//$data = chr(27).chr(114).chr(0);
		
		
		//beep
		//$data = chr(7);
		
		//$data = chr(27).chr(25).chr(48);
		
		//27 102 m n
		//$data = chr(27).chr(102).chr(1).chr(20);
		
		//var_dump(printer_list(PRINTER_ENUM_LOCAL|PRINTER_ENUM_REMOTE));
		//$printername = '\\\\A-C909117361694\\EPSON TM-U220 Receipt';
		//$data = "hello\r\n\r\n";
		$result = true;
		if($ph = printer_open($printername)) { 
					
		   // Set print mode to RAW and send data to printer 
		   printer_set_option($ph, PRINTER_MODE, "RAW"); 
		   printer_write($ph, $data); 
		   printer_close($ph); 
		} else {
			$result = false;
		}
		var_dump($result);
	}
	
	public function handler($method="") {
		if(strlen($method)>0) {
			$div = CDivElement::factory();
			$w = $div->add_widget();
			$w->add($method);
			cajax::reload($w);
			exit;
		}
		$app = CApp::instance();
		
		$widget1 = $app->add_widget();
		
		$actions = $widget1->add_div()->add_class('row-fluid')->add_div()->add_class('span12')->add_action_list();
        $actadd = $actions->add_action();
        $actadd->set_label(" " . clang::__("Add") . " " . clang::__("Store"))->set_icon("plus");
		$handler = $actadd->add_listener('click')->add_handler('reload');
		$handler->set_target('ajax-target')->set_url(curl::base().'ctest/handler/test1');
		
		$widget1->add_widget()->add_div('ajax-target');
		echo $app->render();
		
	}
}