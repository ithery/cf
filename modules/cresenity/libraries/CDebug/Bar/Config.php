<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 1:09:21 PM
 */
class CDebug_Bar_Config {
    protected $config = [];

    public function __construct(array $config = []) {
        $this->config = $config;
    }

    public function setOptions(array $config = []) {
        $this->config = $config;
        return $this;
    }
}
