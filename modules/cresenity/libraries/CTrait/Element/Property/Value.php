<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:18:23 AM
 */
trait CTrait_Element_Property_Value {
    /**
     * @var int
     */
    protected $value;

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue() {
        return $this->value;
    }
}
