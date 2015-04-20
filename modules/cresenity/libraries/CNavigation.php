<?php

class CNavigation extends CObject {

    public static $_instance = array();
    protected $navs;

    protected function __construct($app_code = null) {

        if ($app_code == null) {
            $app_code = CF::app_code();
        }
		
		$path = '';
		$temp_path = '';
		$org_code = CF::org_code();
		if(strlen($org_code)>0) {
			if(strlen($path)==0) {
				$temp_path = DOCROOT . 'application' . DIRECTORY_SEPARATOR . $app_code . DIRECTORY_SEPARATOR . $org_code. DIRECTORY_SEPARATOR. 'config' . DIRECTORY_SEPARATOR;
				if (is_file($temp_path . 'nav' . EXT)) {
					$path = DOCROOT . 'application' . DIRECTORY_SEPARATOR . $app_code . DIRECTORY_SEPARATOR . $org_code. DIRECTORY_SEPARATOR. 'config' . DIRECTORY_SEPARATOR;
				}
			}
		}
		if(strlen($path)==0) {
			$temp_path = DOCROOT . 'application' . DIRECTORY_SEPARATOR . $app_code . DIRECTORY_SEPARATOR .'default'. DIRECTORY_SEPARATOR .'config' . DIRECTORY_SEPARATOR;
			if (is_file($temp_path . 'nav' . EXT)) {
				$path = DOCROOT . 'application' . DIRECTORY_SEPARATOR . $app_code . DIRECTORY_SEPARATOR .'default'. DIRECTORY_SEPARATOR .'config' . DIRECTORY_SEPARATOR;
			}
		}
		if(strlen($path)==0) {
		
				$path = DOCROOT . 'config' . DIRECTORY_SEPARATOR . 'nav' . DIRECTORY_SEPARATOR . $app_code . DIRECTORY_SEPARATOR;
		}
        $this->navs = null;

        if (is_file($path . 'nav' . EXT)) {

            $this->navs = include $path . 'nav' . EXT;
            /*
              unset($config);
              require $path.'nav'.EXT;

              if(is_array($config)&&isset($config["cnav"])) {
              $this->navs = $config["cnav"];
              }
              unset($config);
             */
        }
    }

    public static function instance($app_code = null) {
        if ($app_code == null)
            $app_code = CApp::instance()->code();
        if (!isset($_instance[$app_code]))
            self::$_instance[$app_code] = new CNavigation($app_code);
        return self::$_instance[$app_code];
    }

    public function navs() {
        return $this->navs;
    }

    public function render() {

        return cnav::render();
    }

}

