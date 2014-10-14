<?php

class CListener extends CObject {

    protected $event;
    protected $handlers;
    protected $owner;
    protected $confirm;

    protected function __construct($owner, $event) {
        parent::__construct();


        $this->owner = $owner;
        $this->handlers = array();
        $this->confirm = false;
        $this->event = $event;
    }

    public static function factory($owner, $event) {

        return new CListener($owner, $event);
    }

    public function set_confirm($bool) {
        $this->confirm = $bool;
        return $this;
    }
	
	public function owner() {
		return $this->owner;
	}
	public function set_owner($owner) {
		$this->owner=$owner;
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
        $handler = CHandler::factory($this->owner,$this->event,$handler_name);
        $this->handlers[] = $handler;
        return $handler;
    }

    public function js($indent) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $handlers_script = "";
        foreach ($this->handlers as $handler) {
            $handlers_script.= $handler->js();
        }
        $confirm_start_script = "";
        $confirm_end_script = "";
        if ($this->confirm) {


            $confirm_start_script = "
				bootbox.confirm('Are you sure?', function(confirmed) {
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

