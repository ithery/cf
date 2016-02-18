<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  May 13, 2015
     * @license http://piposystem.com Piposystem
     */
    class CFormInputTooltip extends CFormInput {

        protected $content;
        protected $label;
        protected $placement;
        protected $listener;

        public function __construct($id = "") {
            parent::__construct($id);

            $this->content = '';
            $this->label = 'tooltip';
            $this->placement = 'right';
            $this->listener = 'hover';
        }

        public static function factory($id = "") {
            return new CFormInputTooltip($id);
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();

            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) {
                $classes = " " . $classes;
            }

            $html->appendln('<div id="' . $this->id . '" class = "bs-popover-tooltip-wrapper ' .$classes .'">');
            $html->appendln('    <div id="' . $this->id . '-tooltip" class = "bs-popover-tooltip" data-listener="' . $this->listener . '"
                data-target="' . $this->id . '-tooltip-content" data-placement="' . $this->placement . '">');
            $html->appendln($this->label);
            $html->appendln('    </div>');
            $html->appendln('    <div id="' . $this->id . '-tooltip-content" class="hide">');
            $html->appendln($this->content);
            $html->appendln('    </div>');
            $html->appendln('</div>');

            $html->appendln("<style type='text/css'>
                    .bs-popover-tooltip-wrapper, .bs-popover-tooltip {
                        display: inline;
                    }
                </style>");
            $html->appendln(parent::html($indent));
            return $html->text();
        }

        public function js($indent = 0) {
            $js = new CStringBuilder();
            $js->appendln('
                jQuery("#' . $this->id . '-tooltip").popover({
                    placement : "' . $this->placement . '",
                    trigger : "' . $this->listener . '",
                    html : true, 
                    content: function() {
                        var id = jQuery(this).attr("data-target");
                        return jQuery(this).parent().find("#" + id).html();
                    },
                });
                ');
            $js->appendln(parent::js($indent));
            return $js->text();
        }

        function get_content() {
            return $this->content;
        }

        function get_label() {
            return $this->label;
        }

        function get_placement() {
            return $this->placement;
        }

        function get_listener() {
            return $this->listener;
        }

        function set_content($content) {
            $this->content = $content;
            return $this;
        }

        function set_label($label) {
            $this->label = $label;
            return $this;
        }

        function set_placement($placement) {
            $this->placement = $placement;
            return $this;
        }

        function set_listener($listener) {
            $this->listener = $listener;
            return $this;
        }

    }
    