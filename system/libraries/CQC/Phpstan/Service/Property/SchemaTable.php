<?php

/** @see https://github.com/psalm/laravel-psalm-plugin/blob/master/src/SchemaTable.php */
final class CQC_Phpstan_Service_Property_SchemaTable {
    public string $name;

    /**
     * @var array<string, SchemaColumn>
     */
    public array $columns = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function setColumn(CQC_Phpstan_Service_Property_SchemaColumn $column): void {
        $this->columns[$column->name] = $column;
    }

    public function renameColumn(string $oldName, string $newName): void {
        if (!isset($this->columns[$oldName])) {
            return;
        }

        $oldColumn = $this->columns[$oldName];

        unset($this->columns[$oldName]);

        $oldColumn->name = $newName;

        $this->columns[$newName] = $oldColumn;
    }

    public function dropColumn(string $columnName): void {
        unset($this->columns[$columnName]);
    }
}
