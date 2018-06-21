<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 4:18:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Factory {

    /**
     * 
     * @deprecated since version 1.2, please use createControl
     * @param string $id
     * @param string $type
     * @return CElement_FormInput
     */
    public static function create_control($id, $type) {
        return self::createControl($id, $type);
    }

    /**
     * 
     * @deprecated since version 1.2, please use createControl
     * @param string $fieldId
     * @return CElement_Component_Form_Field
     */
    public static function create_field($fieldId = "") {
        return self::createField($fieldId);
    }

    /**
     * 
     * @deprecated since version 1.2, please use createTable
     * @param string $tableId
     * @return CElement_Component_DataTable
     */
    public static function create_table($tableId = "") {
        return self::createTable($tableId);
    }

}
