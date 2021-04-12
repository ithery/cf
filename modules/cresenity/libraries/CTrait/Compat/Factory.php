<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 4:18:47 AM
 */
 //@codingStandardsIgnoreStart
trait CTrait_Compat_Factory {
    /**
     * @param string $id
     * @param string $type
     *
     * @deprecated since version 1.2, please use createControl
     *
     * @return CElement_FormInput
     */
    public static function create_control($id, $type) {
        return self::createControl($id, $type);
    }

    /**
     * @param string $fieldId
     *
     * @deprecated since version 1.2, please use createField
     *
     * @return CElement_Component_Form_Field
     */
    public static function create_field($fieldId = '') {
        return self::createField($fieldId);
    }

    /**
     * @param string $tableId
     *
     * @deprecated since version 1.2, please use createTable
     *
     * @return CElement_Component_DataTable
     */
    public static function create_table($tableId = '') {
        return self::createTable($tableId);
    }
}
