<?php

class CElement_Component_DataTable_Renderer {
    protected $table;

    public function __construct(CElement_Component_DataTable $table) {
        $this->table = $table;
    }

    public static function checkboxCell($table, $row) {
        $checkboxValue = $table->getCheckboxValue();
        $rowKey = '';
        if (is_array($row)) {
            if (array_key_exists($table->getKeyField(), $row)) {
                $rowKey = $row[$table->getKeyField()];
            }
        }

        if ($row instanceof CModel) {
            $rowKey = $row->{$table->getKeyField()};
        }

        $checkboxChecked = in_array($rowKey, $checkboxValue) ? ' checked="checked"' : '';
        $checkboxId = $table->id() . '-' . $rowKey;

        return '
            <div class="capp-table-checkbox-wrapper">
                <input type="checkbox" class="checkbox-' . $table->id() . '" name="' . $table->id() . '-check[]" id="' . $checkboxId . '" value="' . $rowKey . '"' . $checkboxChecked . '>
                <label for="' . $checkboxId . '"></label>
            </div>
        ';
    }
}
