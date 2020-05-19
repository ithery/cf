<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 1:38:49 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File_Config_FileManager extends CManager_File_ConfigAbstract{

    public function __construct(array $options) {

        $config = CF::config('filemanager');
        $this->options = array_merge($config, $options);
    }

}
