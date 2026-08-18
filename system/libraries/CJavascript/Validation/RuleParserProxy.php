<?php

defined('SYSPATH') or die('No direct access allowed.');

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
