<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 1:23:42 PM
 */
class CJavascript_Validation_RuleParserProxy {
    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param array|string $rules
     *
     * @return array
     */
    public function parse($rules) {
        return CValidation_RuleParser::parse($rules);
    }
}
