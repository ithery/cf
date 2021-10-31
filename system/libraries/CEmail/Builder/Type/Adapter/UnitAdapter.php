<?php

class CEmail_Builder_Type_Adapter_UnitAdapter extends CEmail_Builder_Type_AbstractAdapter {
    const MATCHER = '/^(unit|unitWithNegative)\(.*\)/im';
    const TYPE = 'unit';

    public function __construct($typeConfig, $value) {
        parent::__construct($typeConfig, $value);

        $allowNegRegex = '';
        if (preg_match('/^unitWithNegative/', $typeConfig)) {
            $allowNegRegex = '-|';
        }
        $units = [];

        if (preg_match('#\(([^)]+)\)#im', $typeConfig, $matches)) {
            $units = explode(',', carr::get($matches, 1));
        } else {
            throw new Exception('unit not found : ' . $typeConfig);
        }
        $args = ['1'];
        if (preg_match('#\{([^}]+)\}#im', $typeConfig, $matches)) {
            $args = explode(',', carr::get($matches, 1));
        }
        $allowAutoRegex = in_array('auto', $units) ? '|auto' : '';
        $filteredUnits = carr::filter($units, function ($u) {
            return $u !== 'auto';
        });
        $filteredUnitsRegex = implode('|', carr::map($filteredUnits, function ($item) {
            return cstr::escapeRegExp($item);
        }));
        $argsRegex = implode(',', $args);
        $this->errorMessage = 'has invalid value: $value for type Unit, only accepts (' . implode(',', $units) . ') units and ' . implode(' to ', $args) . ' value(s)';
        $this->matchers = ['^(((' . $allowNegRegex . '\\d|,|\\.){1,}(' . $filteredUnitsRegex . ')|0' . $allowAutoRegex . ')( )?){' . $argsRegex . '}$'];
    }
}
