<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 1:41:17 PM
 */

/**
 * Schema Visitor used for Validation or Generation purposes.
 */
interface CDatabase_Schema_Visitor_Interface {
    /**
     * @param CDatabase_Schema $schema
     *
     * @return void
     */
    public function acceptSchema(CDatabase_Schema $schema);

    /**
     * @param CDatabase_Schema_Table $table
     *
     * @return void
     */
    public function acceptTable(CDatabase_Schema_Table $table);

    /**
     * @param CDatabase_Schema_Table  $table
     * @param CDatabase_Schema_Column $column
     *
     * @return void
     */
    public function acceptColumn(CDatabase_Schema_Table $table, CDatabase_Schema_Column $column);

    /**
     * @param CDatabase_Schema_Table                $localTable
     * @param CDatabase_Schema_ForeignKeyConstraint $fkConstraint
     *
     * @return void
     */
    public function acceptForeignKey(CDatabase_Schema_Table $localTable, CDatabase_Schema_ForeignKeyConstraint $fkConstraint);

    /**
     * @param CDatabase_Schema_Table $table
     * @param CDatabase_Schema_Index $index
     *
     * @return void
     */
    public function acceptIndex(CDatabase_Schema_Table $table, CDatabase_Schema_Index $index);

    /**
     * @param CDatabase_Schema_Sequence $sequence
     *
     * @return void
     */
    public function acceptSequence(CDatabase_Schema_Sequence $sequence);
}
