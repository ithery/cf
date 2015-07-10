<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Apr 17, 2015
     * @license http://piposystem.com Piposystem
     */
    class CTableRow extends CElement {

        protected $columns = array();

        public function __construct($id = "") {
            parent::__construct($id);
        }

        public static function factory($id = "") {
            return new CTableRow($id);
        }

        public function add_column($content) {
            $this->columns[] = $content;
            return $this;
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $html->appendln('<tr>');

            foreach ($this->columns as $column_k => $arr_column_v) {
                if (!is_array($arr_column_v))
                        $arr_column_v = array($arr_column_v);
                $html->appendln('<td>');
                foreach ($arr_column_v as $column_v) {
                    if ($column_v instanceof CRenderable) {
                        $column_v = $column_v->html();
                    }
                    $html->appendln($column_v)->br();
                }
                $html->appendln('</td>');
            }
            $html->appendln('</tr>');

            return $html->text();
        }

        public function js($indent = 0) {
            $js = new CStringBuilder();
            $js->set_indent($indent);
			$js->append(parent::js($indent))->br();
			
            foreach ($this->columns as $column_k => $column_v) {
                if ($column_v instanceof CRenderable) {
                    $js->append($column_v->js());
                }
            }
			

            return $js->text();
        }

    }
    