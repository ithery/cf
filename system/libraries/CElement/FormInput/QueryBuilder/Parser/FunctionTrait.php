<?php

use CElement_FormInput_QueryBuilder_Constant as Constant;

trait CElement_FormInput_QueryBuilder_Parser_FunctionTrait {
    protected $operator_sql = [
        Constant::FILTER_OPERATOR_EQUAL => ['operator' => '='],
        'not_equal' => ['operator' => '!='],
        'in' => ['operator' => 'IN'],
        'not_in' => ['operator' => 'NOT IN'],
        'less' => ['operator' => '<'],
        'less_or_equal' => ['operator' => '<='],
        'greater' => ['operator' => '>'],
        'greater_or_equal' => ['operator' => '>='],
        'between' => ['operator' => 'BETWEEN'],
        'not_between' => ['operator' => 'NOT BETWEEN'],
        'begins_with' => ['operator' => 'LIKE',     'prepend' => '%'],
        'not_begins_with' => ['operator' => 'NOT LIKE', 'prepend' => '%'],
        'contains' => ['operator' => 'LIKE',     'append' => '%', 'prepend' => '%'],
        'not_contains' => ['operator' => 'NOT LIKE', 'append' => '%', 'prepend' => '%'],
        'ends_with' => ['operator' => 'LIKE',     'append' => '%'],
        'not_ends_with' => ['operator' => 'NOT LIKE', 'append' => '%'],
        'is_empty' => ['operator' => '='],
        'is_not_empty' => ['operator' => '!='],
        'is_null' => ['operator' => 'NULL'],
        'is_not_null' => ['operator' => 'NOT NULL']
    ];

    protected $needs_array = [
        'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN',
    ];

    /**
     * @param stdClass $rule
     */
    abstract protected function checkRuleCorrect(stdClass $rule);

    /**
     * Determine if an operator (LIKE/IN) requires an array.
     *
     * @param $operator
     *
     * @return bool
     */
    protected function operatorRequiresArray($operator) {
        return in_array($operator, $this->needs_array);
    }

    /**
     * Determine if an operator is NULL/NOT NULL.
     *
     * @param $operator
     *
     * @return bool
     */
    protected function operatorIsNull($operator) {
        return ($operator == 'NULL' || $operator == 'NOT NULL') ? true : false;
    }

    /**
     * Make sure that a condition is either 'or' or 'and'.
     *
     * @param $condition
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException
     *
     * @return string
     */
    protected function validateCondition($condition) {
        $condition = trim(strtolower($condition));

        if ($condition !== 'and' && $condition !== 'or') {
            throw new CElement_FormInput_QueryBuilder_Exception_ParseException("Condition can only be one of: 'and', 'or'.");
        }

        return $condition;
    }

    /**
     * Enforce whether the value for a given field is the correct type.
     *
     * @param bool   $requireArray value must be an array
     * @param mixed  $value        the value we are checking against
     * @param string $field        the field that we are enforcing
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException if value is not a correct type
     *
     * @return mixed value after enforcement
     */
    protected function enforceArrayOrString($requireArray, $value, $field) {
        $this->checkFieldIsAnArray($requireArray, $value, $field);

        if (!$requireArray && is_array($value)) {
            return $this->convertArrayToFlatValue($field, $value);
        }

        return $value;
    }

    /**
     * Ensure that a given field is an array if required.
     *
     * @param bool $requireArray
     * @param $value
     * @param string $field
     *
     * @see enforceArrayOrString
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException
     */
    protected function checkFieldIsAnArray($requireArray, $value, $field) {
        if ($requireArray && !is_array($value)) {
            throw new CElement_FormInput_QueryBuilder_Exception_ParseException("Field (${field}) should be an array, but it isn't.");
        }
    }

    /**
     * Convert an array with just one item to a string.
     *
     * In some instances, and array may be given when we want a string.
     *
     * @param string $field
     * @param $value
     *
     * @see enforceArrayOrString
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException
     *
     * @return mixed
     */
    protected function convertArrayToFlatValue($field, $value) {
        if (count($value) !== 1) {
            throw new CElement_FormInput_QueryBuilder_Exception_ParseException("Field (${field}) should not be an array, but it is.");
        }

        return $value[0];
    }

    /**
     * Convert a Datetime field to Carbon items to be used for comparisons.
     *
     * @param $value
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException
     *
     * @return \CCarbon|\CCarbon[]
     */
    protected function convertDatetimeToCarbon($value) {
        if (is_array($value)) {
            return array_map(function ($v) {
                return new CCarbon($v);
            }, $value);
        }

        return new CCarbon($value);
    }

    /**
     * Append or prepend a string to the query if required.
     *
     * @param bool  $requireArray value must be an array
     * @param mixed $value        the value we are checking against
     * @param mixed $sqlOperator
     *
     * @return mixed $value
     */
    protected function appendOperatorIfRequired($requireArray, $value, $sqlOperator) {
        if (!$requireArray) {
            if (isset($sqlOperator['append'])) {
                $value = $sqlOperator['append'] . $value;
            }

            if (isset($sqlOperator['prepend'])) {
                $value = $value . $sqlOperator['prepend'];
            }
        }

        return $value;
    }

    /**
     * Decode the given JSON.
     *
     * @param string|array $json
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException
     *
     * @return stdClass
     */
    private function decodeJSON($json) {
        if (is_array($json)) {
            $json = json_encode($json);
        }
        $query = json_decode($json);
        if (json_last_error()) {
            throw new CElement_FormInput_QueryBuilder_Exception_ParseException('JSON parsing threw an error: ' . json_last_error_msg());
        }

        if (!is_object($query)) {
            throw new CElement_FormInput_QueryBuilder_Exception_ParseException('The query is not valid JSON');
        }

        return $query;
    }

    /**
     * Get a value for a given rule.
     *
     * throws an exception if the rule is not correct.
     *
     * @param stdClass $rule
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_RuleException
     */
    private function getRuleValue(stdClass $rule) {
        if (!$this->checkRuleCorrect($rule)) {
            throw new CElement_FormInput_QueryBuilder_Exception_RuleException();
        }

        return $rule->value;
    }

    /**
     * Check that a given field is in the allowed list if set.
     *
     * @param $fields
     * @param $field
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException
     */
    private function ensureFieldIsAllowed($fields, $field) {
        if (is_array($fields) && !in_array($field, $fields)) {
            throw new CElement_FormInput_QueryBuilder_Exception_ParseException("Field ({$field}) does not exist in fields list");
        }
    }

    /**
     * Some types of SQL Operators (ie, those that deal with lists/arrays) have specific requirements.
     * This function enforces those requirements.
     * makeQuery, for arrays.
     *
     * @param CModel_Query|CDatabase_Query_Builder $query
     * @param stdClass                             $rule
     * @param array                                $sqlOperator
     * @param array                                $value
     * @param string                               $condition
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException
     *
     * @return CModel_Query|CDatabase_Query_Builder
     */
    protected function makeQueryWhenArray($query, stdClass $rule, array $sqlOperator, array $value, $condition) {
        if ($sqlOperator['operator'] == 'IN' || $sqlOperator['operator'] == 'NOT IN') {
            return $this->makeArrayQueryIn($query, $rule, $sqlOperator['operator'], $value, $condition);
        } elseif ($sqlOperator['operator'] == 'BETWEEN' || $sqlOperator['operator'] == 'NOT BETWEEN') {
            return $this->makeArrayQueryBetween($query, $rule, $sqlOperator['operator'], $value, $condition);
        }

        throw new CElement_FormInput_QueryBuilder_Exception_ParseException('makeQueryWhenArray could not return a value');
    }

    /**
     * Create a 'null' query when required.
     *
     * @param CModel_Query|CDatabase_Query_Builder $query
     * @param stdClass                             $rule
     * @param array                                $sqlOperator
     * @param string                               $condition
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException when SQL operator is !null
     *
     * @return CModel_Query|CDatabase_Query_Builder
     */
    protected function makeQueryWhenNull($query, stdClass $rule, array $sqlOperator, $condition) {
        if ($sqlOperator['operator'] == 'NULL') {
            return $query->whereNull($rule->field, $condition);
        } elseif ($sqlOperator['operator'] == 'NOT NULL') {
            return $query->whereNotNull($rule->field, $condition);
        }

        throw new CElement_FormInput_QueryBuilder_Exception_ParseException('makeQueryWhenNull was called on an SQL operator that is not null');
    }

    /**
     * MakeArrayQueryIn, when the query is an IN or NOT IN...
     *
     * @param CModel_Query|CDatabase_Query_Builder $query
     * @param stdClass                             $rule
     * @param string                               $operator
     * @param array                                $value
     * @param string                               $condition
     *
     * @see makeQueryWhenArray
     *
     * @return CModel_Query|CDatabase_Query_Builder
     * @phpstan-ignore-next-line
     */
    private function makeArrayQueryIn($query, stdClass $rule, $operator, array $value, $condition) {
        if ($operator == 'NOT IN') {
            /** @phpstan-ignore-next-line */
            return $query->whereNotIn($rule->field, $value, $condition);
        }

        /** @phpstan-ignore-next-line */
        return $query->whereIn($rule->field, $value, $condition);
    }

    /**
     * MakeArrayQueryBetween, when the query is a BETWEEN or NOT BETWEEN...
     *
     * @param CModel_Query|CDatabase_Query_Builder $query
     * @param stdClass                             $rule
     * @param string                               $operator  the SQL operator used. [BETWEEN|NOT BETWEEN]
     * @param array                                $value
     * @param string                               $condition
     *
     * @see makeQueryWhenArray
     *
     * @throws CElement_FormInput_QueryBuilder_Exception_ParseException when more then two items given for the between
     *
     * @return CModel_Query|CDatabase_Query_Builder
     * @phpstan-ignore-next-line
     */
    private function makeArrayQueryBetween($query, stdClass $rule, $operator, array $value, $condition) {
        if (count($value) !== 2) {
            throw new CElement_FormInput_QueryBuilder_Exception_ParseException("{$rule->field} should be an array with only two items.");
        }

        if ($operator == 'NOT BETWEEN') {
            /** @phpstan-ignore-next-line */
            return $query->whereNotBetween($rule->field, $value, $condition);
        }

        /** @phpstan-ignore-next-line */
        return $query->whereBetween($rule->field, $value, $condition);
    }
}
