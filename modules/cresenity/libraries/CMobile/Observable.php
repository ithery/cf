<?php

abstract class CMobile_Observable extends CRenderable {

    protected $listeners;
    protected $manager;
    protected $wrapper;

    public function get_listeners() {
        return $this->listeners;
    }

    public function add_listener($event) {
        $listener = CMobile_Listener::factory($this->id, $event);
        $this->listeners[] = $listener;
        
        return $listener;
    }

    protected function __construct($id = "") {

        parent::__construct($id);
        $this->wrapper = $this;
        $this->listeners = array();
        $this->manager = CManager::instance();
        $this->manager->register_control('text', 'CMobile_Element_Control_Input_Text');
        $this->manager->register_control('hidden', 'CMobile_Element_Control_Input_Hidden');
        $this->manager->register_control('email', 'CMobile_Element_Control_Input_Email');
        $this->manager->register_control('password', 'CMobile_Element_Control_Input_Password');
        $this->manager->register_control('radio', 'CMobile_Element_Control_Input_Radio');
        $this->manager->register_control('checkbox', 'CMobile_Element_Control_Input_Checkbox');
        $this->manager->register_control('search', 'CMobile_Element_Control_Input_Search');
        $this->manager->register_control('date', 'CMobile_Element_Control_Input_Date');
        $this->manager->register_control('time', 'CMobile_Element_Control_Input_Time');
        $this->manager->register_control('file', 'CMobile_Element_Control_Input_File');
        $this->manager->register_control('textarea', 'CMobile_Element_Control_Textarea');
        $this->manager->register_control('select', 'CMobile_Element_Control_Select');
    }

    /**
     * @param string $eventName The name of the event
     * @param PhpExt_Listener|PhpExt_JavascriptStm $listener A {@link PhpExt_JavascriptStm} with the corresponding name of the javascript function previously defined of a {@link PhpExt_Listener} to create an anonymous function
     * @return PhpExt_Observable 
     */
    public function attach_listener($event, $listener) {
        $this->listeners->add($listener, $event);
        return $this;
    }

    public function detach_listener($event) {
        $this->listeners->remove($event);
    }

    /**
     * 
     * @param type $id
     * @param type $type
     * @return CFormInput
     */
    public function add_control($id, $type) {
        $control = null;
        if ($this->manager->is_registered_control($type)) {
            $control = $this->manager->create_control($id, $type);
        } else {
            trigger_error('Unknown control type ' . $type);
        }


        $this->wrapper->add($control);

        return $control;
    }

    public function add_swiper($field_id = "") {
        $field = CMobile_Element_Component_Swiper::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }
    public function add_slick($field_id = "") {
        $field = CMobile_Element_Component_Slick::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }
    public function add_iframe($field_id = "") {
        $field = CMobile_Element_Iframe::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }
    public function add_video($field_id = "") {
        $field = CMobile_Element_Video::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }
    public function add_source($field_id = "") {
        $field = CMobile_Element_Source::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }
    public function add_message($field_id = "") {
        $field = CMobile_Element_Message::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }
    
    public function add_nav($field_id = "") {
        $field = CMobile_Element_Component_Nav::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }
    
    public function add_side_nav($field_id = "") {
        $field = CMobile_Element_Component_SideNav::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_menu($field_id = "") {
        $field = CMobile_Element_Component_SideNav_Menu::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }


    public function add_chip($field_id = "") {
        $field = CMobile_Element_Component_Chip::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_collection($field_id = "") {
        $field = CMobile_Element_Component_Collection::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_progress($field_id = "") {
        $field = CMobile_Element_Component_Progress::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_switch($field_id = "") {
        $field = CMobile_Element_Component_Switch::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_carousel($field_id = "") {
        $field = CMobile_Element_Component_Carousel::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_file($field_id = "") {
        $field = CMobile_Element_Control_File::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_collapsible($field_id = "") {
        $field = CMobile_Element_Component_Collapsible::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_field($field_id = "") {
        $field = CMobile_Element_FormField::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_a($field_id = "") {
        $field = CMobile_Element_A::factory($field_id);
        $this->wrapper->add($field);
        return $field;
    }

    public function add_action_list($id = "") {
        $actlist = CMobile_Element_ActionList::factory($id);
        $this->wrapper->add($actlist);
        if ($this instanceof CMobile_Form) {
            $actlist->set_style('form-action');
        }
        return $actlist;
    }

    public function add_floating_action($id = "") {
        $element = CMobile_Element_FloatingAction::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    public function add_icon($id = "") {
        $element = CMobile_Element_Icon::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    public function add_action($id = "") {
        $element = CMobile_Element_Action::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    public function add_card($id = "") {
        $element = CMobile_Element_Component_Card::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /* BASIC ELEMENT */

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Div
     */
    public function add_div($id = "") {
        $div = CMobile_Element_Div::factory($id);
        $this->wrapper->add($div);
        return $div;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Span
     */
    public function add_span($id = "") {
        $element = CMobile_Element_Span::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Img
     */
    public function add_img($id = "") {
        $element = CMobile_Element_Img::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Form
     */
    public function add_form($id = "") {
        $element = CMobile_Element_Form::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Label
     */
    public function add_label($field_id = "") {
        $elm = CMobile_Element_Label::factory($field_id);
        $this->wrapper->add($elm);
        return $elm;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Ul
     */
    public function add_ul($id = "") {
        $element = CMobile_Element_Ul::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Ul
     */
    public function add_ol($id = "") {
        $element = CMobile_Element_Ol::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Li
     */
    public function add_li($id = "") {
        $element = CMobile_Element_Li::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Table
     */
    public function add_table($id = "") {
        $element = CMobile_Element_Table::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Tr
     */
    public function add_tr($id = "") {
        $element = CMobile_Element_Tr::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Td
     */
    public function add_td($id = "") {
        $element = CMobile_Element_Td::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Th
     */
    public function add_th($id = "") {
        $element = CMobile_Element_Th::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_H1
     */
    public function add_h1($id = "") {
        $element = CMobile_Element_H1::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_H2
     */
    public function add_h2($id = "") {
        $element = CMobile_Element_H2::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_H3
     */
    public function add_h3($id = "") {
        $element = CMobile_Element_H3::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_H4
     */
    public function add_h4($id = "") {
        $element = CMobile_Element_H4::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_H5
     */
    public function add_h5($id = "") {
        $element = CMobile_Element_H5::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_H6
     */
    public function add_h6($id = "") {
        $element = CMobile_Element_H6::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_P
     */
    public function add_p($id = "") {
        $element = CMobile_Element_P::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Blockquote
     */
    public function add_blockquote($id = "") {
        $element = CMobile_Element_Blockquote::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Hr
     */
    public function add_hr($id = "") {
        $element = CMobile_Element_Hr::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    /**
     * 
     * @param string $id
     * @return CMobile_Element_Br
     */
    public function add_br($id = "") {
        $element = CMobile_Element_Br::factory($id);
        $this->wrapper->add($element);
        return $element;
    }

    public function add_element($type, $id = "") {
        $element = null;
        if ($this->manager->is_registered_element($type)) {
            $element = $this->manager->create_element($id, $type);
        } else {
            trigger_error('Unknown element type ' . $type);
        }



        $this->wrapper->add($element);

        return $element;
    }

    public function set_handler_url_param($param) {

        foreach ($this->listeners as $listener) {
            $listener->set_handler_url_param($param);
        }
    }

    public static function is_instanceof($value) {
        if (is_object($value)) {
            return ($value instanceof CObject);
        }
        return false;
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        foreach ($this->listeners as $listener) {
            $js->appendln($listener->js($js->get_indent()));
        }

        $js->appendln(parent::js($js->get_indent()))->br();

        return $js->text();
    }

    public function regenerate_id($recursive = false) {
        $before_id = $this->id;
        parent::regenerate_id($recursive);
        //we change the owner of listener
        foreach ($this->listeners as $listener) {
            if ($listener->owner() == $before_id) {
                $listener->set_owner($this->id);
            }
        }
    }

}
