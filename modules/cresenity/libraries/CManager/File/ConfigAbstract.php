<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 28, 2019, 3:00:12 AM
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
