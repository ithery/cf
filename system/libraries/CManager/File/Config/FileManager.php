<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Config_FileManager extends CManager_File_ConfigAbstract {
    public function __construct(array $options) {
        $config = CF::config('filemanager');
        $this->options = array_merge($config, $options);
    }
}
