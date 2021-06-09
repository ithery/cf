<?php

/**
 * Provides the behavior, features and SQL dialect of the MySQL 5.7 (5.7.9 GA) database platform.
 */
class CDatabase_Platform_MySql57 extends CDatabase_Platform_MySql {
    /**
     * {@inheritdoc}
     */
    public function hasNativeJsonType() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonTypeDeclarationSQL(array $column) {
        return 'JSON';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPreAlterTableRenameIndexForeignKeySQL(CDatabase_Schema_Table_Diff $diff) {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getPostAlterTableRenameIndexForeignKeySQL(CDatabase_Schema_Table_Diff $diff) {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRenameIndexSQL($oldIndexName, CDatabase_Schema_Index $index, $tableName) {
        return ['ALTER TABLE ' . $tableName . ' RENAME INDEX ' . $oldIndexName . ' TO ' . $index->getQuotedName($this)];
    }

    /**
     * {@inheritdoc}
     */
    protected function getReservedKeywordsClass() {
        return CDatabase_Platform_Keywords_MySql80::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeDoctrineTypeMappings() {
        parent::initializeDoctrineTypeMappings();

        $this->doctrineTypeMapping['json'] = CDatabase_Type::JSON;
    }
}
