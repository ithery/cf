<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 4:18:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CColor_Trait_OptionsTrait {

    protected $options = array();

    public function getOption($key) {
        return carr::get($this->options, $key);
    }

    public function setOption($key, $value) {
        $this->options[$key] = $value;
        return $this;
    }

    public function haveOption($key) {
        return $this->getOption($key) !== null;
    }

}
