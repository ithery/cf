<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 1:23:42 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_Validation_RuleParserProxy {

    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param array|string $rules
     * @return array
     */
    public function parse($rules) {
        return CValidation_RuleParser::parse($rules);
    }

}
