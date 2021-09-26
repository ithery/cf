<?php

class CElement_Component_DataTable_Renderer {
    protected $table;

    public function __construct(CElement_Component_DataTable $table) {
        $this->table = $table;
    }

    public static function checkboxCell($table, $row) {
        $checkboxValue = $table->getCheckboxValue();
        $rowKey = '';
        if (array_key_exists($table->getKeyField(), $row)) {
            $rowKey = $row[$table->getKeyField()];
        }
        $checkboxChecked = in_array($rowKey, $checkboxValue) ? ' checked="checked"' : '';

        return '
            <div class="capp-table-checkbox-wrapper">
                <input type="checkbox" class="checkbox-' . $table->id() . '" name="' . $table->id() . '-check[]" id="' . $table->id() . '-' . $rowKey . '" value="' . $rowKey . '"' . $checkboxChecked . '>
            </div>
        ';
    }
}
