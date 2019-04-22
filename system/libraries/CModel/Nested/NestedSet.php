<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2018, 5:42:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Nested_NestedSet {

    /**
     * The name of default lft column.
     */
    const LFT = 'lft';

    /**
     * The name of default depth column.
     */
    const DEPTH = 'depth';

    /**
     * The name of default rgt column.
     */
    const RGT = 'rgt';

    /**
     * The name of default parent id column.
     */
    const PARENT_ID = 'parent_id';

    /**
     * Insert direction.
     */
    const BEFORE = 1;

    /**
     * Insert direction.
     */
    const AFTER = 2;

    /**
     * Add default nested set columns to the table. Also create an index.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     */
    public static function columns(Blueprint $table) {
        $table->unsignedInteger(self::LFT)->default(0);
        $table->unsignedInteger(self::RGT)->default(0);
        $table->unsignedInteger(self::DEPTH)->default(0);
        $table->unsignedInteger(self::PARENT_ID)->nullable();
        $table->index(static::getDefaultColumns());
    }

    /**
     * Drop NestedSet columns.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     */
    public static function dropColumns(Blueprint $table) {
        $columns = static::getDefaultColumns();
        $table->dropIndex($columns);
        $table->dropColumn($columns);
    }

    /**
     * Get a list of default columns.
     *
     * @return array
     */
    public static function getDefaultColumns() {
        return [static::LFT, static::RGT, static::DEPTH, static::PARENT_ID];
    }

    /**
     * Replaces instanceof calls for this trait.
     *
     * @param mixed $node
     *
     * @return bool
     */
    public static function isNode($node) {
        return is_object($node) && in_array(CModel_Nested_Trait::class, (array) $node);
    }

}
