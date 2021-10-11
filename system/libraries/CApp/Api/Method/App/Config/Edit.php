<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_App_Config_Edit extends CApp_Api_Method_App {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $key = c::get($this->request(), 'key');
        $newValue = c::get($this->request(), 'newValue');
        $data = [];

        try {
            $appConfigFile = DOCROOT . 'application' . DS . $this->appCode() . DS . 'default' . DS . 'config' . DS . 'app' . EXT;
            $config = CConfig::instance('app');
            $config->addAppCode($this->appCode());
            foreach ($config->getConfigData() as $d) {
                if (c::get($d, 'key') == $key) {
                    $configRecord = $d;
                    break;
                }
            }
            $type = c::get($configRecord, 'type');

            $currentConfig = [];
            if (file_exists($appConfigFile)) {
                $currentConfig = include $appConfigFile;
            }
            if ($type == 'boolean') {
                if ($newValue == 0) {
                    $newValue = false;
                } else {
                    $newValue = true;
                }
            }
            c::set($currentConfig, $key, $newValue);
            $data = $currentConfig;
            $data['path'] = $appConfigFile;
            CFile::putPhpValue($appConfigFile, $currentConfig);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
