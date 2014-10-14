<?php defined('SYSPATH') OR die('No direct access allowed.');

require_once dirname(__FILE__)."/Lib/elfinder/elFinderConnector.class.php";
require_once dirname(__FILE__)."/Lib/elfinder/elFinder.class.php";
require_once dirname(__FILE__)."/Lib/elfinder/elFinderVolumeDriver.class.php";
require_once dirname(__FILE__)."/Lib/elfinder/elFinderVolumeLocalFileSystem.class.php";


class CElfinder  {
	private $folder;
	private $driver;
	public function __construct() {
		$this->folder = "";
		$this->driver = "LocalFileSystem";
	}
	
	public static function factory() {
		return new CElfinder();
	}
	
	public function set_folder($folder) {
		$this->folder = $folder;
		return $this;
	}
	public function access($attr, $path, $data, $volume) {
		return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
	}
	public function run() {
	
		$url = curl::base('http',false).$this->folder;
		$path = $this->folder;
		$opts = array(
			// 'debug' => true,
			'roots' => array(
				array(
					'driver'        => $this->driver,   // driver for accessing file system (REQUIRED)
					'path'          => $path,         // path to files (REQUIRED)
					'URL'           => $url, // URL to files (REQUIRED)
					'accessControl' => array($this,'access')             // disable and hide dot starting files (OPTIONAL)
				)
			)
		);

		$connector = new elFinderConnector(new elFinder($opts));
		$connector->run();
	}

}