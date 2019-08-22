<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 12:14:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Schema Diff.
 *
 */
class CDatabase_Schema_Diff {

    /**
     * @var CDatabase_Schema
     */
    public $fromSchema;

    /**
     * All added namespaces.
     *
     * @var string[]
     */
    public $newNamespaces = [];

    /**
     * All removed namespaces.
     *
     * @var string[]
     */
    public $removedNamespaces = [];

    /**
     * All added tables.
     *
     * @var CDatabase_Schema_Table[]
     */
    public $newTables = [];

    /**
     * All changed tables.
     *
     * @var CDatabase_Schema_Table_Diff[]
     */
    public $changedTables = [];

    /**
     * All removed tables.
     *
     * @var CDatabase_Schema_Table[]
     */
    public $removedTables = [];

    /**
     * @var CDatabase_Schema_Sequence[]
     */
    public $newSequences = [];

    /**
     * @var CDatabase_Schema_Sequence[]
     */
    public $changedSequences = [];

    /**
     * @var CDatabase_Schema_Sequence[]
     */
    public $removedSequences = [];

    /**
     * @var CDatabase_Schema_ForeignKeyConstraint[]
     */
    public $orphanedForeignKeys = [];

    /**
     * Constructs an SchemaDiff object.
     *
     * @param CDatabase_Schema_Table[]     $newTables
     * @param CDatabase_Schema_TableDiff[] $changedTables
     * @param CDatabase_Schema_Table[]     $removedTables
     * @param CDatabase_Schema|null $fromSchema
     */
    public function __construct($newTables = [], $changedTables = [], $removedTables = [], CDatabase_Schema $fromSchema = null) {
        $this->newTables = $newTables;
        $this->changedTables = $changedTables;
        $this->removedTables = $removedTables;
        $this->fromSchema = $fromSchema;
    }

    /**
     * The to save sql mode ensures that the following things don't happen:
     *
     * 1. Tables are deleted
     * 2. Sequences are deleted
     * 3. Foreign Keys which reference tables that would otherwise be deleted.
     *
     * This way it is ensured that assets are deleted which might not be relevant to the metadata schema at all.
     *
     * @param CDatabase_Platform $platform
     *
     * @return array
     */
    public function toSaveSql(CDatabase_Platform $platform) {
        return $this->_toSql($platform, true);
    }

    /**
     * @param CDatabase_Platform $platform
     *
     * @return array
     */
    public function toSql(CDatabase_Platform $platform) {
        return $this->_toSql($platform, false);
    }

    /**
     * @param CDatabase_Platform $platform
     * @param bool                                      $saveMode
     *
     * @return array
     */
    protected function _toSql(CDatabase_Platform $platform, $saveMode = false) {
        $sql = [];

        if ($platform->supportsSchemas()) {
            foreach ($this->newNamespaces as $newNamespace) {
                $sql[] = $platform->getCreateSchemaSQL($newNamespace);
            }
        }

        if ($platform->supportsForeignKeyConstraints() && $saveMode == false) {
            foreach ($this->orphanedForeignKeys as $orphanedForeignKey) {
                $sql[] = $platform->getDropForeignKeySQL($orphanedForeignKey, $orphanedForeignKey->getLocalTable());
            }
        }

        if ($platform->supportsSequences() == true) {
            foreach ($this->changedSequences as $sequence) {
                $sql[] = $platform->getAlterSequenceSQL($sequence);
            }

            if ($saveMode === false) {
                foreach ($this->removedSequences as $sequence) {
                    $sql[] = $platform->getDropSequenceSQL($sequence);
                }
            }

            foreach ($this->newSequences as $sequence) {
                $sql[] = $platform->getCreateSequenceSQL($sequence);
            }
        }

        $foreignKeySql = [];
        foreach ($this->newTables as $table) {
            $sql = array_merge(
                    $sql, $platform->getCreateTableSQL($table, CDatabase_Platform::CREATE_INDEXES)
            );

            if (!$platform->supportsForeignKeyConstraints()) {
                continue;
            }
            foreach ($table->getForeignKeys() as $foreignKey) {
                $foreignKeySql[] = $platform->getCreateForeignKeySQL($foreignKey, $table);
            }
        }
        $sql = array_merge($sql, $foreignKeySql);

        if ($saveMode === false) {
            foreach ($this->removedTables as $table) {
                $sql[] = $platform->getDropTableSQL($table);
            }
        }

        foreach ($this->changedTables as $tableDiff) {
            $sql = array_merge($sql, $platform->getAlterTableSQL($tableDiff));
        }

        return $sql;
    }

}
