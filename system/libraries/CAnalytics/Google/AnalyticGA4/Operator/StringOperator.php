<?php

use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;

class CAnalytics_Google_AnalyticGA4_Operator_StringOperator extends CAnalytics_Google_AnalyticGA4_OperatorAbstract {
    protected $matchType;

    protected $value;

    protected $caseSensitive = false;

    public function __construct() {
        parent::__construct();
    }

    public function exact($value) {
        $this->matchType = MatchType::EXACT;
        $this->value = $value;

        return $this;
    }

    public function beginsWith($value) {
        $this->matchType = MatchType::BEGINS_WITH;
        $this->value = $value;

        return $this;
    }

    public function endsWith($value) {
    }

    public function contains($value) {
    }

    public function fullRegexp($value) {
    }

    public function partialRegexp($value) {
    }

    /**
     * @return \Google\Analytics\Data\V1beta\Filter\StringFilter
     */
    public function toGA4Object() {
        return new StringFilter([
            'match_type' => $this->matchType,
            'value' => $this->value,
            'case_sensitive' => $this->caseSensitive
        ]);
    }
}
