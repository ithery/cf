<?php

class CEmail_Builder_Type_Adapter_EnumAdapter extends CEmail_Builder_Type_AbstractAdapter {
    const MATCHER = '/^enum/im';
    const TYPE = 'enum';

    public function __construct($typeConfig, $value) {
        parent::__construct($typeConfig, $value);
        $matchers = [];
        if (preg_match('/\(([^)]+)\)/', $typeConfig, $matches)) {
            $matchers = explode(',', carr::get($matches, 1));
        }
        $this->errorMessage = 'has invalid value: $value for type Unit, only accepts ' . implode(',', $matchers) . '';
        $this->matchers = carr::map($matches, function ($m) {
            return '^' . cstr::escapeRegExp($m) . '$';
        });
    }
}
