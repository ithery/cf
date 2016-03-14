<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Progress extends CMobile_Element_AbstractComponent {

	protected $circle;
	protected $progress;
	protected $indeterminate;
	protected $active;
	protected $size;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
        $this->circle = false;
        $this->indeterminate = false;
        $this->active = false;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Progress($id);
    }

    public function set_circle($circle) {
    	$this->circle = $circle;
        return $this;
    }

    public function set_size($size) {
    	$this->size = $size;
        return $this;
    }

    public function set_active($active) {
    	$this->active = $active;
        return $this;
    }

    public function set_progress($progress) {
    	$this->progress = $progress;
        return $this;
    }

    public function set_indeterminate($indeterminate) {
    	$this->indeterminate = $indeterminate;
        return $this;
    }

	public function build() {
		if($this->circle) {
			$this->add_class('preloader-wrapper');
			if(strlen($this->size) > 0) {
				$this->add_class($this->size);
			} else {
				$this->add_class('small');
			}
			if($this->active) {
				$this->add_class('active');
			}
			$this->add('<div class="spinner-layer spinner-red-only">
					      <div class="circle-clipper left">
					        <div class="circle"></div>
					      </div><div class="gap-patch">
					        <div class="circle"></div>
					      </div><div class="circle-clipper right">
					        <div class="circle"></div>
					      </div>
					    </div>');
		} else {
			$this->add_class('progress');
			$progress = '0';
			if(strlen($this->progress) > 0) {
				$progress = $this->progress;
			}
			if($this->indeterminate) {
				$this->add('<div class="indeterminate"></div>');
			} else {
				$this->add('<div class="determinate" style="width: ' . $progress . '%"></div>');
			}
		}
	}

	public function js($indent=0) {
		$js = "";
		$js.=parent::js();
		return $js;
	}
}
