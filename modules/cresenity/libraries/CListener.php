<?php

    class CListener extends CObject {

        protected $event;
        protected $handlers;
        protected $owner;
        protected $confirm;
        protected $confirm_message;

        protected function __construct($owner, $event) {
            parent::__construct();


            $this->owner = $owner;
            $this->handlers = array();
            $this->confirm = false;
            $this->confirm_message = "";
            $this->event = $event;
        }

        public static function factory($owner, $event) {

            return new CListener($owner, $event);
        }

        public function set_confirm($bool) {
            $this->confirm = $bool;
            return $this;
        }
        
        public function set_confirm_message($message) {
            $this->confirm_message = $message;
            return $this;
        }

        public function owner() {
            return $this->owner;
        }

        public function set_owner($owner) {
            $this->owner = $owner;
            //we set all handler owner too
            foreach ($this->handlers as $handler) {
                $handler->set_owner($owner);
            }
            return $this;
        }

        public function handlers() {
            return $this->handlers;
        }

        public function set_handler_url_param($param) {

            foreach ($this->handlers as $handler) {
                $handler->set_url_param($param);
            }
        }

        public function add_handler($handler_name) {
            $handler = CHandler::factory($this->owner, $this->event, $handler_name);
            $this->handlers[] = $handler;
            return $handler;
        }

        public function js($indent = 0) {
            $js = new CStringBuilder();
            $js->set_indent($indent);
            $handlers_script = "";
            foreach ($this->handlers as $handler) {
                $handlers_script.= $handler->js();
            }
            $confirm_start_script = "";
            $confirm_end_script = "";
            if ($this->confirm) {

                $confirm_message=$this->confirm_message;
                if(strlen($confirm_message)==0) {
                    $confirm_message = 'Are you sure?';
                }
                $confirm_start_script = "
				bootbox.confirm('".$confirm_message."', function(confirmed) {
					if(confirmed) {
			";

                $confirm_end_script = "
					}
				});
			";
            }

            $js->append("
			jQuery('#" . $this->owner . "')." . $this->event . "(function() {				
				
				" . $confirm_start_script . "
				" . $handlers_script . "
				" . $confirm_end_script . "
				
			});
		");


            return $js->text();
        }

    }
    