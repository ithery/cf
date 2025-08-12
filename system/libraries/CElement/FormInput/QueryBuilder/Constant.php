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
                'apply_to' => ['string', 'number', 'datetime'],
                'multiple' => false,
            ],
            self::FILTER_OPERATOR_NOT_EQUAL => [
                'accept_values' => true,
                'apply_to' => ['string', 'number', 'datetime'],
                'multiple' => false,
            ],
            self::FILTER_OPERATOR_IN => [
                'accept_values' => true,
                'apply_to' => ['string', 'number', 'datetime'],
                'multiple' => true,
            ],
            self::FILTER_OPERATOR_NOT_IN => [
                'accept_values' => true,
                'apply_to' => ['string', 'number', 'datetime'],
                'multiple' => true,
            ],
            self::FILTER_OPERATOR_LESS => [
                'accept_values' => true,
                'apply_to' => ['number', 'datetime'],
                'multiple' => false,
            ],
            self::FILTER_OPERATOR_LESS_OR_EQUAL => [
                'accept_values' => true,
                'apply_to' => ['number', 'datetime']
            ],
            self::FILTER_OPERATOR_GREATER => [
                'accept_values' => true,
                'apply_to' => ['number', 'datetime']
            ],
            self::FILTER_OPERATOR_GREATER_OR_EQUAL => [
                'accept_values' => true,
                'apply_to' => ['number', 'datetime']
            ],
            self::FILTER_OPERATOR_BETWEEN => [
                'accept_values' => true,
                'apply_to' => ['number', 'datetime']
            ],
            self::FILTER_OPERATOR_NOT_BETWEEN => [
                'accept_values' => true,
                'apply_to' => ['number', 'datetime']
            ],
            self::FILTER_OPERATOR_BEGINS_WITH => [
                'accept_values' => true,
                'apply_to' => ['string']
            ],
            self::FILTER_OPERATOR_NOT_BEGINS_WITH => [
                'accept_values' => true,
                'apply_to' => ['string']
            ],
            self::FILTER_OPERATOR_CONTAINS => [
                'accept_values' => true,
                'apply_to' => ['string']
            ],
            self::FILTER_OPERATOR_NOT_CONTAINS => [
                'accept_values' => true,
                'apply_to' => ['string']
            ],
            self::FILTER_OPERATOR_ENDS_WITH => [
                'accept_values' => true,
                'apply_to' => ['string']
            ],
            self::FILTER_OPERATOR_NOT_ENDS_WITH => [
                'accept_values' => true,
                'apply_to' => ['string']
            ],
            self::FILTER_OPERATOR_IS_EMPTY => [
                'accept_values' => false,
                'apply_to' => ['string']
            ],
            self::FILTER_OPERATOR_IS_NOT_EMPTY => [
                'accept_values' => false,
                'apply_to' => ['string']
            ],
            self::FILTER_OPERATOR_IS_NULL => [
                'accept_values' => false,
                'apply_to' => ['string', 'number', 'datetime']
            ],
            self::FILTER_OPERATOR_IS_NOT_NULL => [
                'accept_values' => false,
                'apply_to' => ['string', 'number', 'datetime']
            ]
        ];
    }
}
