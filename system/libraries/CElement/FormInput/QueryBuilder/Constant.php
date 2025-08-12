<?php

final class CElement_FormInput_QueryBuilder_Constant {
    const FILTER_TYPE_STRING = 'string';

    const FILTER_TYPE_INTEGER = 'integer';

    const FILTER_TYPE_DOUBLE = 'double';

    const FILTER_TYPE_DATE = 'date';

    const FILTER_TYPE_TIME = 'time';

    const FILTER_TYPE_DATETIME = 'datetime';

    const FILTER_TYPE_BOOLEAN = 'boolean';

    const FILTER_INPUT_TEXT = 'text';

    const FILTER_INPUT_NUMBER = 'number';

    const FILTER_INPUT_TEXTAREA = 'textarea';

    const FILTER_INPUT_RADIO = 'radio';

    const FILTER_INPUT_CHECKBOX = 'number';

    const FILTER_INPUT_SELECT = 'select';

    const FILTER_OPERATOR_EQUAL = 'equal';

    const FILTER_OPERATOR_NOT_EQUAL = 'not_equal';

    const FILTER_OPERATOR_IN = 'in';

    const FILTER_OPERATOR_NOT_IN = 'not_in';

    const FILTER_OPERATOR_LESS = 'less';

    const FILTER_OPERATOR_LESS_OR_EQUAL = 'less_or_equal';

    const FILTER_OPERATOR_GREATER = 'greater';

    const FILTER_OPERATOR_GREATER_OR_EQUAL = 'greater_or_equal';

    const FILTER_OPERATOR_BETWEEN = 'between';

    const FILTER_OPERATOR_NOT_BETWEEN = 'not_between';

    const FILTER_OPERATOR_BEGINS_WITH = 'begins_with';

    const FILTER_OPERATOR_NOT_BEGINS_WITH = 'not_begins_with';

    const FILTER_OPERATOR_CONTAINS = 'contains';

    const FILTER_OPERATOR_NOT_CONTAINS = 'not_contains';

    const FILTER_OPERATOR_ENDS_WITH = 'ends_with';

    const FILTER_OPERATOR_NOT_ENDS_WITH = 'not_ends_with';

    const FILTER_OPERATOR_IS_EMPTY = 'is_empty';

    const FILTER_OPERATOR_IS_NOT_EMPTY = 'is_not_empty';

    const FILTER_OPERATOR_IS_NULL = 'is_null';

    const FILTER_OPERATOR_IS_NOT_NULL = 'is_not_null';

    public static function getOperatorData() {
        return [
            self::FILTER_OPERATOR_EQUAL => [
                'accept_values' => true,
                'apply_to' => ['string', 'number', 'datetime']
            ],
            'not_equal' => ['accept_values' => true,  'apply_to' => ['string', 'number', 'datetime']],
            'in' => [
                'accept_values' => true,
                'apply_to' => ['string', 'number', 'datetime'],
                'multiple' => true,
            ],
            'not_in' => [
                'accept_values' => true,
                'apply_to' => ['string', 'number', 'datetime'],
                'multiple' => true,
            ],
            'less' => ['accept_values' => true,  'apply_to' => ['number', 'datetime']],
            'less_or_equal' => ['accept_values' => true,  'apply_to' => ['number', 'datetime']],
            'greater' => ['accept_values' => true,  'apply_to' => ['number', 'datetime']],
            'greater_or_equal' => ['accept_values' => true,  'apply_to' => ['number', 'datetime']],
            'between' => ['accept_values' => true,  'apply_to' => ['number', 'datetime']],
            'not_between' => ['accept_values' => true,  'apply_to' => ['number', 'datetime']],
            'begins_with' => ['accept_values' => true,  'apply_to' => ['string']],
            'not_begins_with' => ['accept_values' => true,  'apply_to' => ['string']],
            'contains' => ['accept_values' => true,  'apply_to' => ['string']],
            'not_contains' => ['accept_values' => true,  'apply_to' => ['string']],
            'ends_with' => ['accept_values' => true,  'apply_to' => ['string']],
            'not_ends_with' => ['accept_values' => true,  'apply_to' => ['string']],
            'is_empty' => ['accept_values' => false, 'apply_to' => ['string']],
            'is_not_empty' => ['accept_values' => false, 'apply_to' => ['string']],
            'is_null' => ['accept_values' => false, 'apply_to' => ['string', 'number', 'datetime']],
            'is_not_null' => ['accept_values' => false, 'apply_to' => ['string', 'number', 'datetime']]
        ];
    }
}
