<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 4:18:16 AM
 */
trait CColor_Trait_OptionsTrait {
    protected $options = [];

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
