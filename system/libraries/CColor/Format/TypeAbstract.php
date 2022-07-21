<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:26:59 AM
 */
class CColor_Format_TypeAbstract implements CColor_Format_TypeInterface {
    protected $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function __toString() {
        if (is_string($this->value)) {
            return $this->value;
        }
        if (is_array($this->value) || is_object($this->value)) {
            return json_encode($this->value);
        }
        return (string) $this->value;
    }

    public function value() {
        return $this->value;
    }
}
