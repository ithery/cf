<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:09:14 AM
 */
class CDatabase_Schema_Manager_Mysql extends CDatabase_Schema_Manager {
    public function getDatabaseRowCount() {
        $databaseName = $this->db->getDatabaseName();
        return $this->db->getValue('
    	    	SELECT SUM(table_rows)
    	    	FROM INFORMATION_SCHEMA.TABLES
    	    	WHERE table_schema = ' . $this->db->escape($databaseName) . '
    	    	GROUP BY table_schema
    	    ');
    }

    public function getDatabaseSize() {
        $databaseName = $this->db->getDatabaseName();
        return $this->db->getValue('
    	    	SELECT SUM(data_length + index_length)
    	    	FROM INFORMATION_SCHEMA.TABLES
    	    	WHERE table_schema = ' . $this->db->escape($databaseName) . '
    	    	GROUP BY table_schema
    	    ');
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableViewDefinition($view) {
        return new CView_View($view['TABLE_NAME'], $view['VIEW_DEFINITION']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableDefinition($table) {
        return array_shift($table);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableUserDefinition($user) {
        return [
            'user' => $user['User'],
            'password' => $user['Password'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableIndexesList($tableIndexes, $tableName = null) {
        foreach ($tableIndexes as $k => $v) {
            $v = array_change_key_case($v, CASE_LOWER);
            if ($v['key_name'] === 'PRIMARY') {
                $v['primary'] = true;
            } else {
                $v['primary'] = false;
            }
            if (strpos($v['index_type'], 'FULLTEXT') !== false) {
                $v['flags'] = ['FULLTEXT'];
            } elseif (strpos($v['index_type'], 'SPATIAL') !== false) {
                $v['flags'] = ['SPATIAL'];
            }
            $tableIndexes[$k] = $v;
        }

        return parent::_getPortableTableIndexesList($tableIndexes, $tableName);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableSequenceDefinition($sequence) {
        return end($sequence);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableDatabaseDefinition($database) {
        return $database['Database'];
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableColumnDefinition($tableColumn) {
        $tableColumn = array_change_key_case($tableColumn, CASE_LOWER);

        $dbType = strtolower($tableColumn['type']);
        $dbType = strtok($dbType, '(), ');
        $length = isset($tableColumn['length']) ? $tableColumn['length'] : strtok('(), ');

        $fixed = null;

        if (!isset($tableColumn['name'])) {
            $tableColumn['name'] = '';
        }

        $scale = null;
        $precision = null;

        $type = $this->platform->getDoctrineTypeMapping($dbType);

        // In cases where not connected to a database DESCRIBE $table does not return 'Comment'
        if (isset($tableColumn['comment'])) {
            $type = $this->extractDoctrineTypeFromComment($tableColumn['comment'], $type);
            $tableColumn['comment'] = $this->removeDoctrineTypeFromComment($tableColumn['comment'], $type);
        }

        switch ($dbType) {
            case 'char':
            case 'binary':
                $fixed = true;
                break;
            case 'float':
            case 'double':
            case 'real':
            case 'numeric':
            case 'decimal':
                if (preg_match('([A-Za-z]+\(([0-9]+)\,([0-9]+)\))', $tableColumn['type'], $match)) {
                    $precision = $match[1];
                    $scale = $match[2];
                    $length = null;
                }
                break;
            case 'tinytext':
                $length = CDatabase_Platform_Mysql::LENGTH_LIMIT_TINYTEXT;
                break;
            case 'text':
                $length = CDatabase_Platform_Mysql::LENGTH_LIMIT_TEXT;
                break;
            case 'mediumtext':
                $length = CDatabase_Platform_Mysql::LENGTH_LIMIT_MEDIUMTEXT;
                break;
            case 'tinyblob':
                $length = CDatabase_Platform_Mysql::LENGTH_LIMIT_TINYBLOB;
                break;
            case 'blob':
                $length = CDatabase_Platform_Mysql::LENGTH_LIMIT_BLOB;
                break;
            case 'mediumblob':
                $length = CDatabase_Platform_Mysql::LENGTH_LIMIT_MEDIUMBLOB;
                break;
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'integer':
            case 'bigint':
            case 'year':
                $length = null;
                break;
        }

        if ($this->platform instanceof CDatabase_Platform_MariaDb1027) {
            $columnDefault = $this->getMariaDb1027ColumnDefault($this->platform, $tableColumn['default']);
        } else {
            $columnDefault = $tableColumn['default'];
        }

        $options = [
            'length' => $length !== null ? (int) $length : null,
            'unsigned' => strpos($tableColumn['type'], 'unsigned') !== false,
            'fixed' => (bool) $fixed,
            'default' => $columnDefault,
            'notnull' => $tableColumn['null'] !== 'YES',
            'scale' => null,
            'precision' => null,
            'autoincrement' => strpos($tableColumn['extra'], 'auto_increment') !== false,
            'comment' => isset($tableColumn['comment']) && $tableColumn['comment'] !== '' ? $tableColumn['comment'] : null,
        ];

        if ($scale !== null && $precision !== null) {
            $options['scale'] = (int) $scale;
            $options['precision'] = (int) $precision;
        }
        $column = new CDatabase_Schema_Column($tableColumn['field'], CDatabase_Type::getType($type), $options);

        if (isset($tableColumn['collation'])) {
            $column->setPlatformOption('collation', $tableColumn['collation']);
        }

        return $column;
    }

    /**
     * Return Doctrine/Mysql-compatible column default values for MariaDB 10.2.7+ servers.
     *
     * - Since MariaDb 10.2.7 column defaults stored in information_schema are now quoted
     *   to distinguish them from expressions (see MDEV-10134).
     * - CURRENT_TIMESTAMP, CURRENT_TIME, CURRENT_DATE are stored in information_schema
     *   as current_timestamp(), currdate(), currtime()
     * - Quoted 'NULL' is not enforced by Maria, it is technically possible to have
     *   null in some circumstances (see https://jira.mariadb.org/browse/MDEV-14053)
     * - \' is always stored as '' in information_schema (normalized)
     *
     * @link https://mariadb.com/kb/en/library/information-schema-columns-table/
     * @link https://jira.mariadb.org/browse/MDEV-13132
     *
     * @param null|string $columnDefault default value as stored in information_schema for MariaDB >= 10.2.7
     */
    private function getMariaDb1027ColumnDefault(CDatabase_Platform_MariaDb1027 $platform, $columnDefault = null) {
        if ($columnDefault === 'NULL' || $columnDefault === null) {
            return null;
        }
        if ($columnDefault[0] === "'") {
            return stripslashes(
                str_replace(
                    "''",
                    "'",
                    preg_replace('/^\'(.*)\'$/', '$1', $columnDefault)
                )
            );
        }
        switch ($columnDefault) {
            case 'current_timestamp()':
                return $platform->getCurrentTimestampSQL();
            case 'curdate()':
                return $platform->getCurrentDateSQL();
            case 'curtime()':
                return $platform->getCurrentTimeSQL();
        }
        return $columnDefault;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableForeignKeysList($tableForeignKeys) {
        $list = [];
        foreach ($tableForeignKeys as $value) {
            $value = array_change_key_case($value, CASE_LOWER);
            if (!isset($list[$value['constraint_name']])) {
                if (!isset($value['delete_rule']) || $value['delete_rule'] === 'RESTRICT') {
                    $value['delete_rule'] = null;
                }
                if (!isset($value['update_rule']) || $value['update_rule'] === 'RESTRICT') {
                    $value['update_rule'] = null;
                }

                $list[$value['constraint_name']] = [
                    'name' => $value['constraint_name'],
                    'local' => [],
                    'foreign' => [],
                    'foreignTable' => $value['referenced_table_name'],
                    'onDelete' => $value['delete_rule'],
                    'onUpdate' => $value['update_rule'],
                ];
            }
            $list[$value['constraint_name']]['local'][] = $value['column_name'];
            $list[$value['constraint_name']]['foreign'][] = $value['referenced_column_name'];
        }

        $result = [];
        foreach ($list as $constraint) {
            $result[] = new CDatabase_Schema_ForeignKeyConstraint(
                array_values($constraint['local']),
                $constraint['foreignTable'],
                array_values($constraint['foreign']),
                $constraint['name'],
                [
                    'onDelete' => $constraint['onDelete'],
                    'onUpdate' => $constraint['onUpdate'],
                ]
            );
        }

        return $result;
    }
}
