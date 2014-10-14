<?php defined('SYSPATH') OR die('No direct access allowed.');
class File_manager_Controller extends CController {
	
	public function index() {
		$app = CApp::instance();
		$app->title(clang::__("File Manager"));

			
		$app->add_div('elfinder');
		
		$url = curl::base()."admin/core/elfinder_connector";
		
		$js="
			$().ready(function() {
				var elf = $('#elfinder').elfinder({
					uiOptions : {
						// toolbar configuration
						toolbar : [
							['back', 'forward'],
							['reload'],
							['home', 'up'],
							['mkdir', 'mkfile', 'upload'],
							['open', 'download', 'getfile'],
							['info'],
							['quicklook'],
							['copy', 'cut', 'paste'],
							['rm'],
							['duplicate', 'rename', 'edit', 'resize'],
							['extract', 'archive'],
							['search'],
							['view']
						],

						// directories tree options
						tree : {
							// expand current root on init
							openRootOnLoad : true,
							// auto load current dir parents
							syncTree : true
						},

						// navbar options
						navbar : {
							minWidth : 150,
							maxWidth : 500
						},

						// current working directory options
						cwd : {
							// display parent directory in listing as \"..\"
							oldSchool : false
						}
					},
					
					lang: 'en',             // language (OPTIONAL)
					url : '".$url."'  // connector URL (REQUIRED)
				}).elfinder('instance');			
			});
		";
		
		$app->add_js($js);
		
		echo $app->render();
	}

	
	
	
	
	
} // End Home Controller