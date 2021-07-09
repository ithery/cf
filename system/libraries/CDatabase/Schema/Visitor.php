<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 1:39:53 PM
 */

/**
 * Abstract Visitor with empty methods for easy extension.
 */
class CDatabase_Schema_Visitor implements CDatabase_Schema_Visitor_Interface, CDatabase_Schema_Visitor_NamespaceInterface {
    /**
     * @param CDatabase_Schema $schema
     */
    public function acceptSchema(CDatabase_Schema $schema) {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptNamespace($namespaceName) {
    }

    /**
     * @param CDatabase_Schema_Table $table
     */
    public function acceptTable(CDatabase_Schema_Table $table) {
    }

    /**
     * @param CDatabase_Schema_Table  $table
     * @param CDatabase_Schema_Column $column
     */
    public function acceptColumn(CDatabase_Schema_Table $table, CDatabase_Schema_Column $column) {
    }

    /**
     * @param CDatabase_Schema_Table                $localTable
     * @param CDatabase_Schema_ForeignKeyConstraint $fkConstraint
     */
    public function acceptForeignKey(CDatabase_Schema_Table $localTable, CDatabase_Schema_ForeignKeyConstraint $fkConstraint) {
    }

    /**
     * @param CDatabase_Schema_Table $table
     * @param CDatabase_Schema_Index $index
     */
    public function acceptIndex(CDatabase_Schema_Table $table, CDatabase_Schema_Index $index) {
    }

    /**
     * @param CDatabase_Schema_Sequence $sequence
     */
    public function acceptSequence(CDatabase_Schema_Sequence $sequence) {
    }
}
