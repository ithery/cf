<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 12, 2019, 8:01:25 PM
 */
class CValidation_Rule_Exists {
    use CValidation_Rule_Trait_DatabaseTrait;

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString() {
        return rtrim(sprintf('exists:%s,%s,%s', $this->table, $this->column, $this->formatWheres()), ',');
    }

    public function __sleep() {
        $this->using = c::collect($this->using)->map(function ($item) {
            return CHelper::closure()->serialize($item);
        })->all();

        return array_keys(get_object_vars($this));
    }

    public function __wakeup() {
        $this->using = c::collect($this->using)->map(function ($item) {
            return CHelper::closure()->unserialize($item);
        })->all();
    }
}
