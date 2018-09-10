<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 4:26:49 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Validation_Rule_Exists extends CApp_Validation_Rule {

    use CApp_Validation_Rule_Trait_DatabaseRuleTrait;

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString() {
        return rtrim(sprintf('exists:%s,%s,%s', $this->table, $this->column, $this->formatWheres()
                ), ',');
    }

}
