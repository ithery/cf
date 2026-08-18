<?php

/**
 * @see CFactory
 * @see CManager
 */
class CManager_Factory {
    /**
     * @param string $id
     * @param string $type
     *
     * @throws Exception
     *
     * @return CElement_FormInput
     */
    public static function createControl($id, $type) {
        return CElement_Factory::createControl($id, $type);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Form_Field
     */
    public static function createField($id = '') {
        return CElement_Factory::createComponent(CElement_Component_Form_Field::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_DataTable
     */
    public static function createTable($id = '') {
        return CElement_Factory::createComponent('DataTable', $id);
    }

    public static function createTableRow($id = '') {
        return CElement_Factory::createComponent(CElement_Component_TableRow::class, $id);
    }

    public static function createSpan($id = '') {
        return CElement_Factory::create(CElement_Element_Span::class, $id);
    }

    public static function createNestable($id = '') {
        $nestable = CElement_Component_Nestable::factory($id);

        return $nestable;
    }

    /**
     * @param string $id
     *
     * @return CElement_Element_Hr
     */
    public static function createHr($id = '') {
        return CElement_Factory::createElement('hr', $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Element_Br
     */
    public static function createBr($id = '') {
        return CElement_Factory::createElement('br', $id);
    }

    public static function createElement($type, $id = '') {
        $element = null;
        if (CManager::instance()->isRegisteredElement($type)) {
            $element = CManager::instance()->createElement($id, $type);
        } else {
            trigger_error('Unknown element type ' . $type);
        }

        return $element;
    }
}
