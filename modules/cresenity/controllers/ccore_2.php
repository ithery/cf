<?php defined('SYSPATH') OR die('No direct access allowed.');
class CCore_Controller extends CController {
	public function index() {
		curl::redirect('');
		
	}
	
	public function js($hash=null) {
		header('content-type: application/javascript');
		if($hash!=null) {
			echo CClientScript::instance()->js($hash);
		} else {
		
			$v = CView::factory('ccore/js');
			echo $v->render();
		}
	
	}
	public function css($hash=null) {
		header('content-type: text/css');
		if($hash!=null) {
			echo CClientScript::instance()->css($hash);
		} else {
			$v = CView::factory('ccore/css');
			echo $v->render();
		}
		
	}
	public function front_js() {
		header('content-type: application/javascript');
		$v = CView::factory('cfront/ccore/js');
		echo $v->render();
	}
	public function front_css() {
		header('content-type: text/css');
		$v = CView::factory('cfront/ccore/css');
		echo $v->render();
	}
	
	public function noimage($width=200, $height=150, $bg_color='EFEFEF', $txt_color='AAAAAA',$text='NO IMAGE' ) {
	
		//Create the image resource 
		$image = ImageCreate($width, $height);
		//Making of colors, we are changing HEX to RGB
		$bg_color = ImageColorAllocate($image, 
					base_convert(substr($bg_color, 0, 2), 16, 10), 
					base_convert(substr($bg_color, 2, 2), 16, 10), 
					base_convert(substr($bg_color, 4, 2), 16, 10));


		$txt_color = ImageColorAllocate($image, 
					base_convert(substr($txt_color, 0, 2), 16, 10), 
					base_convert(substr($txt_color, 2, 2), 16, 10), 
					base_convert(substr($txt_color, 4, 2), 16, 10));

		//Fill the background color 
		ImageFill($image, 0, 0, $bg_color); 
		//Calculating font size   
		$fontsize = ($width>$height)? ($height / 10) : ($width / 10) ;
		if($width<100) {
			$fontsize=1;
		}
		$line_number = 1;
		$total_lines=1;
		$center_x = ceil( ( imagesx($image) - ( ImageFontWidth($fontsize) * strlen($text) ) ) / 2 ); 
		$center_y = ceil( ( ( imagesy($image) - ( ImageFontHeight($fontsize) * $total_lines ) ) / 2)  + ( ($line_number-1) * ImageFontHeight($fontsize) ) ); 
		//Inserting Text    
		imagestring($image, $fontsize, $center_x, $center_y, $text, $txt_color);
		/*
		 imagettftext($image,$fontsize, 0, 
						($width/2) - ($fontsize * 2.75), 
						($height/2) + ($fontsize* 0.2),  
						$txt_color, 'Arial.ttf', $text);
		*/

		//Tell the browser what kind of file is come in 
		header("Content-Type: image/png"); 
		//Output the newly created image in png format 
		imagepng($image);   
		//Free up resources
		ImageDestroy($image);
	}
	
	public function elfinder_connector($folder=null) {
		
		if($folder==null) {
			$app = CApp::instance();
			$folder=DOCROOT."files".DS;
			if(!cfs::is_dir($folder)) cfs::mkdir($folder);
			$folder.=CF::app_code();
			if(!cfs::is_dir($folder)) cfs::mkdir($folder);
			if(ccfg::get('have_user_login')) {
				$user = $app->user();
				if($user!=null) {
					$folder.=DS.$user->username;
					if(!cfs::is_dir($folder)) cfs::mkdir($folder);
				}
			}
		}
		$folder = DOCROOT."files/intern/hery";
		CElfinder::factory()->set_folder($folder)->run();
	}
	
	public function ajax($method) {

        $file = ctemp::makepath("ajax", $method . ".tmp");
        $text = file_get_contents($file);
        $obj = json_decode($text);
        $response = "";
        $db = CDatabase::instance();
        $input = $_POST;
        if ($obj->method == "GET") {
            $input = $_GET;
        }
		
        switch ($obj->type) {
            case "form_process":
                $response = cajax::form_process($obj, $input);
                break;
            case "query":
                $response = cajax::query($obj, $input);
                break;
            case "fillselect":
                $response = cajax::fillselect($obj, $input);
                break;
            case "searchselect":
                $response = cajax::searchselect($obj, $input);
                break;
            case "datatable":
                $response = cajax::datatable($obj, $input);
                break;
			 case "reload":
        	 case "handler_reload":
                $response = cajax::handler_reload($obj, $input);
                break;
        }
        echo $response;
    }
}