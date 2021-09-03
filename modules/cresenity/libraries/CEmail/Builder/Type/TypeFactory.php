<?php

class CEmail_Builder_Type_TypeFactory {
    public static $typeCache = [];
    public static $typeData = [
        'boolean' => [
            'matcher' => CEmail_Builder_Type_Adapter_BooleanAdapter::MATCHER,
            'class' => CEmail_Builder_Type_Adapter_BooleanAdapter::class,
        ],
        'unit' => [
            'matcher' => CEmail_Builder_Type_Adapter_UnitAdapter::MATCHER,
            'class' => CEmail_Builder_Type_Adapter_UnitAdapter::class,
        ],
        'enum' => [
            'matcher' => CEmail_Builder_Type_Adapter_EnumAdapter::MATCHER,
            'class' => CEmail_Builder_Type_Adapter_EnumAdapter::class,
        ],
        'string' => [
            'matcher' => CEmail_Builder_Type_Adapter_StringAdapter::MATCHER,
            'class' => CEmail_Builder_Type_Adapter_StringAdapter::class,
        ],

        'color' => [
            'matcher' => CEmail_Builder_Type_Adapter_ColorAdapter::MATCHER,
            'class' => CEmail_Builder_Type_Adapter_ColorAdapter::class,
        ],
        'integer' => [
            'matcher' => CEmail_Builder_Type_Adapter_IntegerAdapter::MATCHER,
            'class' => CEmail_Builder_Type_Adapter_IntegerAdapter::class,
        ],
    ];

    public static function getAdapter($typeConfig) {
        if (isset(static::$typeCache[$typeConfig])) {
            return static::$typeCache[$typeConfig];
        }
        $record = carr::find(static::$typeData, function ($type) use ($typeConfig) {
            return preg_match(carr::get($type, 'matcher'), $typeConfig);
        });

        if ($record == null) {
            throw new Exception('No type found for ' . $typeConfig);
        }

        $class = $record['class'];
        static::$typeCache[$typeConfig] = $class;
        return static::$typeCache[$typeConfig];
    }
}
