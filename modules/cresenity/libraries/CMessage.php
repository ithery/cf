<?php

    class CMessage extends CElement {

        protected $type;
        protected $message;

        public function __construct($id = "") {
            parent::__construct($id);

            $this->type = "error";
            $this->message = "";
        }

        public function set_type($type) {
            $this->type = $type;
            return $this;
        }

        public function set_message($msg) {
            $this->message = $msg;
            return $this;
        }

        public static function factory($id = "") {
            return new CMessage($id);
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $class = "";
            $icon = "";
            $header = "";
            switch ($this->type) {
                case "warning":
                    $icon = 'fa-warning';
                    $class = " alert-warning";
                    $header = clang::__("Warning") . "!";
                    break;
                case "info":
                    $icon = 'fa-info';
                    $class = " alert-info";
                    $header = clang::__("Info") . "!";
                    break;
                case "success": 
                    $icon = 'fa-check';
                    $class = " alert-success";
                    $header = clang::__("Success") . "!";
                    break;
                default : 
                    $icon = 'fa-ban';
                    $class = " alert-error";
					if($this->bootstrap>=3) {
						$class = " alert-danger";
					}
                    $header = clang::__("Error") . "!";
                    break;
            }
            $html->appendln('<div class="alert alert-dismissible ' . $class . '" role="alert">')->inc_indent()->br();
            if ($this->bootstrap == '3.3') {
                $html->appendln('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>')->br();
                $html->appendln('<h4><i class="icon fa ' .$icon .'"></i>' . $header . '</h4>')->br();
                $html->appendln($this->message)->br();
            }
            else {
                $html->appendln('<a href="#" class="close" data-dismiss="alert">&times;</a>')->br();
                $html->appendln('<strong>' . $header . '</strong> ' . $this->message)->br();
            }

            $html->dec_indent()->appendln('</div>')->br();
            return $html->text();
        }

        public function js($indent = 0) {
            return "";
        }

    }
    