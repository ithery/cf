<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 12:07:32 PM
 */
class CDatabase_Schema_Exception extends CDatabase_Exception {
    const TABLE_DOESNT_EXIST = 10;

    const TABLE_ALREADY_EXISTS = 20;

    const COLUMN_DOESNT_EXIST = 30;

    const COLUMN_ALREADY_EXISTS = 40;

    const INDEX_DOESNT_EXIST = 50;

    const INDEX_ALREADY_EXISTS = 60;

    const SEQUENCE_DOENST_EXIST = 70;

    const SEQUENCE_ALREADY_EXISTS = 80;

    const INDEX_INVALID_NAME = 90;

    const FOREIGNKEY_DOESNT_EXIST = 100;

    const NAMESPACE_ALREADY_EXISTS = 110;

    /**
     * @param string $tableName
     *
     * @return CDatabase_Schema_Exception
     */
    public static function tableDoesNotExist($tableName) {
        return new self("There is no table with name ':tableName' in the schema.", [':tableName' => $tableName], self::TABLE_DOESNT_EXIST);
    }

    /**
     * @param string $indexName
     *
     * @return CDatabase_Schema_Exception
     */
    public static function indexNameInvalid($indexName) {
        return new self('Invalid index-name :indexName given, has to be [a-zA-Z0-9_]', [':indexName' => $indexName], self::INDEX_INVALID_NAME);
    }

    /**
     * @param string $indexName
     * @param string $table
     *
     * @return CDatabase_Schema_Exception
     */
    public static function indexDoesNotExist($indexName, $table) {
        return new self("Index 'indexName' does not exist on table ':table'.", [':indexName' => $indexName, ':table' => $table], self::INDEX_DOESNT_EXIST);
    }

    /**
     * @param string $indexName
     * @param string $table
     *
     * @return CDatabase_Schema_Exception
     */
    public static function indexAlreadyExists($indexName, $table) {
        return new self("An index with name ':indexName' was already defined on table ':table'.", [':indexName' => $indexName, ':table' => $table], self::INDEX_ALREADY_EXISTS);
    }

    /**
     * @param string $columnName
     * @param string $table
     *
     * @return CDatabase_Schema_Exception
     */
    public static function columnDoesNotExist($columnName, $table) {
        return new self("There is no column with name ':columnName' on table ':table'.", [':columnName' => $columnName, ':table' => $table], self::COLUMN_DOESNT_EXIST);
    }

    /**
     * @param string $namespaceName
     *
     * @return CDatabase_Schema_Exception
     */
    public static function namespaceAlreadyExists($namespaceName) {
        return new self(
            "The namespace with name ':namespaceName' already exists.",
            [':namespaceName' => $namespaceName],
            self::NAMESPACE_ALREADY_EXISTS
        );
    }

    /**
     * @param string $tableName
     *
     * @return CDatabase_Schema_Exception
     */
    public static function tableAlreadyExists($tableName) {
        return new self("The table with name ':tableName' already exists.", [':tableName' => $tableName], self::TABLE_ALREADY_EXISTS);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return CDatabase_Schema_Exception
     */
    public static function columnAlreadyExists($tableName, $columnName) {
        return new self(
            "The column ':columnName' on table ':tableName' already exists.",
            [':columnName' => $columnName, ':tableName' => $tableName],
            self::COLUMN_ALREADY_EXISTS
        );
    }

    /**
     * @param string $sequenceName
     *
     * @return CDatabase_Schema_Exception
     */
    public static function sequenceAlreadyExists($sequenceName) {
        return new self("The sequence ':sequenceName' already exists.", [':sequenceName' => $sequenceName], self::SEQUENCE_ALREADY_EXISTS);
    }

    /**
     * @param string $sequenceName
     *
     * @return CDatabase_Schema_Exception
     */
    public static function sequenceDoesNotExist($sequenceName) {
        return new self("There exists no sequence with the name ':sequenceName'.", [':sequenceName' => $sequenceName], self::SEQUENCE_DOENST_EXIST);
    }

    /**
     * @param string $fkName
     * @param string $table
     *
     * @return CDatabase_Schema_Exception
     */
    public static function foreignKeyDoesNotExist($fkName, $table) {
        return new self("There exists no foreign key with the name ':fkName' on table ':table'.", [':fkName' => $fkName, ':table' => $table], self::FOREIGNKEY_DOESNT_EXIST);
    }

    /**
     * @param CDatabase_Schema_Table                $localTable
     * @param CDatabase_Schema_ForeignKeyConstraint $foreignKey
     *
     * @return CDatabase_Schema_Exception
     */
    public static function namedForeignKeyRequired(CDatabase_Schema_Table $localTable, CDatabase_Schema_ForeignKeyConstraint $foreignKey) {
        return new self(
            'The performed schema operation on ' . $localTable->getName() . ' requires a named foreign key, '
                . 'but the given foreign key from (' . implode(', ', $foreignKey->getColumns()) . ') onto foreign table '
                . "'" . $foreignKey->getForeignTableName() . "' (" . implode(', ', $foreignKey->getForeignColumns()) . ') is currently '
                . 'unnamed.'
        );
    }

    /**
     * @param string $changeName
     *
     * @return CDatabase_Schema_Exception
     */
    public static function alterTableChangeNotSupported($changeName) {
        return new self("Alter table change not supported, given '$changeName'");
    }
}
