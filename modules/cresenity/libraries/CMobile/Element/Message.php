<?php

    class CMobile_Element_Message extends CMobile_Element {

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
            return new CMobile_Element_Message($id);
        }

        public function js($indent = 0) {
            $js = new CStringBuilder();
            $js->set_indent($indent);
            $js->append(parent::js($indent))->br();
            $js->append("
              var toastContent = $('<span>" . $this->message . "</span>');
              Materialize.toast(toastContent, 5000);
            ");
            return $js->text();
        }

    }
    