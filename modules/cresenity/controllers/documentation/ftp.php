<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation_Ftp extends CController {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("FTP Library Example"));
        $tab_list = $app->add_tab_list()->set_ajax(true);
		$tab_example = $tab_list->add_tab('example')->set_label('Example')->set_ajax_url(curl::base()."documentation/ftp/example");
		$tab_code = $tab_list->add_tab('code')->set_label('Code')->set_ajax_url(curl::base()."documentation/ftp/code")->add_class('nopadding');
		
        echo $app->render();
    }
	
	public function example() {
		//start example
		$div = CDivElement::factory();
		$host = 'cresenity.com';
		$port = '21';
		$username = 'cresenity';
		$password = 'Project2014';
		
		$url = "ftp://".$username.":".$password."@".$host.":".$port;
		$ftp = CFTP::factory($url);
		
		$div->add('<p>Current Directory:'.$ftp->pwd().'</p>');
		
		$dir = "/public_html";
		if($ftp->chdir($dir)) {
			$div->add('<p>Change Directory to :'.$dir.'</p>');
		} else {
			$div->add('<p>Couldn\'t change directory</p>');
		}
		$div->add('<p>Current Directory:'.$ftp->pwd().'</p>');
		
		$local_file=DOCROOT."temp".DS."ftp_test".DS;
		if(!is_dir($local_file)) mkdir($local_file);
		$local_file.="test";
		if(!file_exists($local_file)) {
			file_put_contents($local_file,'test');
		}
		$div->add('<p>Create Local File:'.$local_file.'</p>');
		$remote_file = '/public_html/test_remote';
		if($ftp->put($remote_file,$local_file)) {
			$div->add('<p>Success Upload to '.$remote_file.' from :'.$local_file.'</p>');
		} else {
			$div->add('<p>Fail Upload to '.$remote_file.' from :'.$local_file.'</p>');
		}
		$local_file = dirname($local_file).DS."test_download";
		if($ftp->get($local_file,$remote_file)) {
			$div->add('<p>Success Download to '.$local_file.' from :'.$remote_file.'</p>');
		} else {
			$div->add('<p>Fail Download to '.$local_file.' from :'.$remote_file.'</p>');
		}
		
		echo $div->json();
		//end example
	}
	
	public function code() {
		$div = CDivElement::factory();
		$file = __FILE__;
		$file_content = file_get_contents($file);
		$content = chtml::specialchars($file_content);
		$content = cstr::between("//start example","//end example",$content);
		
		
		$content = '<pre class="prettyprint linenums">'.$content.'</pre>';
		
		$div->add($content);
		echo $div->json();
	}
}

// End Home Controller