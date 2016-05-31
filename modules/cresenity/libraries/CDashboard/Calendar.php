<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  May 31, 2016
     * @license http://ittron.co.id ITtron
     */
    class CDashboard_Calendar extends CElement_Dashboard {

        public function __construct($id = "", $options = array()) {
            parent::__construct($id, $options);

            CManager::instance()->register_module('fullcalendar');
        }

        public static function factory($id = "", $options = array()) {
            return new CDashboard_Calendar($id, $options);
        }

        public function html($indent = 0) {
            $html = CStringBuilder::factory();

            $query = $this->opt('query');
            $key_field = $this->opt('key_field');
            
            $widget = $this->add_div()->add_class('content')->add_widget()->add_class('dboard-widget-calendar');
            $widget->set_collapse(true);
            $full_calendar = $widget->add_element('full-calendar');
            $full_calendar->set_query($query);
            $full_calendar->set_key_field($key_field);

            $html->append(parent::html($indent));
            return $html->text();
        }

        public function js($indent = 0) {
            $js = CStringBuilder::factory();

            $js->append(parent::js($indent));
            return $js->text();
        }

    }
    