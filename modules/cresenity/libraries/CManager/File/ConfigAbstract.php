<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 28, 2019, 3:00:12 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CManager_File_ConfigAbstract {

    protected $options;

    public function __construct($options) {
        $this->options = $options;
    }

    public function getConfig($key = null, $default = null) {
        if ($key == null) {
            return $this->options;
        }
        return carr::get($this->options, $default);
    }

}
