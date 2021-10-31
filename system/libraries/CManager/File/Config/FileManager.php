<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 1:38:49 AM
 */
class CManager_File_Config_FileManager extends CManager_File_ConfigAbstract {
    public function __construct(array $options) {
        $config = CF::config('filemanager');
        $this->options = array_merge($config, $options);
    }
}
