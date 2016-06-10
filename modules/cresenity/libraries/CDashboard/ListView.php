<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  May 31, 2016
     * @license http://ittron.co.id ITtron
     */
    class CDashboard_ListView extends CElement_Dashboard {

        public function __construct($id = "", $options = array()) {
            parent::__construct($id, $options);
        }

        public static function factory($id = "", $options = array()) {
            return new CDashboard_ListView($id, $options);
        }

        public function html($indent = 0) {
            $html = CStringBuilder::factory();

            $query = $this->opt('query');
            $key_field = $this->opt('key_field');
            $title = $this->opt('title');
            if (strlen($title) == 0) {
                $title = 'ListView';
            }
            $db = CDatabase::instance();
            $r = $db->query($query);
            
            $widget = $this->add_div()->add_class('content')->add_widget()->add_class('dboard-widget-listview default');
            $widget->set_collapse(true)->set_title($title);
            $row = $widget->add_div()->add_class('row listview-items no-margin');
            if ($r->count() == 0) {
                $column = $row->add_div()->add_class('span12 listview-item text-center');
                $column->add(clang::__('No Activities'));
            }
            
            foreach ($r as $k => $v) {
                $date = cobj::get($v, 'date');
                $content = cobj::get($v, 'content');
                $url = cobj::get($v, 'url');
                $other_class = '';
                if (strlen($url) > 0) {
                    $other_class .= ' link ';
                }
                
                $content_str = '';
                $content_str .= $content;
                $content_str .= '<br/>';
                $content_str .= '<span class="date">' .date("d M Y H:i:s", strtotime($date)) .'</span>';
                
                $column = $row->add_div()->add_class('span12 listview-item ' .$other_class);
                
                if (strlen($url) > 0) {
                    $column->add('<a href="' .$url .'" target="_blank">');
                    $column->add($content_str);
                    $column->add('</a>');
                }
                else {
                    $column->add($content_str);
                }
            }

            $html->append(parent::html($indent));
            return $html->text();
        }

        public function js($indent = 0) {
            $js = CStringBuilder::factory();

            $js->append(parent::js($indent));
            return $js->text();
        }

    }
    