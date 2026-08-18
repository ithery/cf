<?php

/**
 * CFactory class.
 *
 * @deprecated  since 1.2 use CElement_Factory
 */
class CFactory {
    use CTrait_Compat_Factory;

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
        return CElement_Factory::createComponent('Form_Field', $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_DataTable
     */
    public static function createTable($id = '') {
        return CElement_Factory::create(CElement_Component_DataTable::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_TableRow
     */
    public static function createTableRow($id = '') {
        return CElement_Factory::createComponent(CElement_Component_TableRow::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_List_TabList
     */
    public static function createTabList($id = '') {
        return CElement_Factory::create(CElement_List_TabList::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Element_Div
     */
    public static function createDiv($id = '') {
        return CElement_Factory::create(CElement_Element_Div::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Element_Span
     */
    public static function createSpan($id = '') {
        return CElement_Factory::create(CElement_Element_Span::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Form
     */
    public static function createForm($id = '') {
        return CElement_Factory::createComponent(CElement_Component_Form::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Nestable
     */
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

    /**
     * @param string $id
     *
     * @return CElement_Element_Img
     */
    public static function createImg($id = '') {
        return CElement_Factory::create(CElement_Element_Img::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Widget
     */
    public static function createWidget($id = '') {
        return CElement_Factory::create(CElement_Component_Widget::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_List_ActionList
     */
    public static function createActionList($id = '') {
        return CElement_Factory::create(CElement_List_ActionList::class, $id);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Action
     */
    public static function createAction($id = '') {
        return CElement_Factory::create(CElement_Component_Action::class, $id);
    }
}
