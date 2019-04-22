<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 12, 2019, 8:01:25 PM
 * @license Ittron Global Teknologi <ittron.co.id>
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

}
