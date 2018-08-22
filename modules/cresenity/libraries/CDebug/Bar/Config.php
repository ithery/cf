<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 1:09:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDebug_Bar_Config {

    protected $config = array();

    public function __construct(array $config = array()) {
        $this->config = $config;
    }

    public function setOptions(array $config = array()) {
        $this->config = $config;
        return $this;
    }

}
