<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Collection_Item extends CMobile_Element_AbstractComponent {

	protected $active;
	protected $text;
	protected $link;
	protected $left_icon;
	protected $left_image;
	protected $right_icon;
	protected $right_image;
	protected $dismissable;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "a";
        $this->text = '';
        $this->link = '';
        $this->left_icon = '';
        $this->left_image = '';
        $this->right_icon = '';
        $this->right_image = '';
		$this->active = false;
		$this->dismissable = false;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Collection_Item($id);
    }

    public function set_active($active) {
        $this->active = $active;
        return $this;
    }

    public function set_dismissable($dismissable) {
        $this->dismissable = $dismissable;
        return $this;
    }

    public function set_text($text) {
		$this->text = $text;
		return $this;
	}

	public function set_link($link) {
		$this->link = $link;
		return $this;
	}

	public function set_left_icon($left_icon) {
		$this->left_icon = $left_icon;
		return $this;
	}

	public function set_left_image($left_image) {
		$this->left_image = $left_image;
		return $this;
	}

	public function set_right_icon($right_icon) {
		$this->right_icon = $right_icon;
		return $this;
	}
	
	public function set_right_image($right_image) {
		$this->right_image = $right_image;
		return $this;
	}

	protected function html_attr() {
        $html_attr = parent::html_attr();
        $link = "";
        
        if (strlen($this->link) > 0) {
            $link = ' href="'.$this->link.'"';
        } else {
        	$link = ' href="#"';
        }
                    
        $html_attr .= $link;
        return $html_attr;
    }

	public function build() {
		$html_attr = $this->html_attr();
		$this->add_class('collection-item');
		if($this->active) {
        	$this->add_class(' active');
        }
        if($this->dismissable) {
        	$this->add_class('dismissable');
        }
        if(strlen($this->left_icon) > 0) {
        	$this->add('<i class="material-icons circle">' . $this->left_icon . '</i>');
        }
        if(strlen($this->left_image) > 0) {
        	$this->add('<img src="' . $this->left_image . '" alt="" class="circle">');
        }
        if(strlen($this->right_icon) > 0 || strlen($this->right_image) > 0) {
        	$this->add('<span class="secondary-content">');
        }
        if(strlen($this->right_icon) > 0) {
        	$this->add('<i class="material-icons circle">' . $this->right_icon . '</i>');
        }
        if(strlen($this->right_image) > 0) {
        	$this->add('<img src="' . $this->right_image . '" alt="" class="circle">');
        }
        if(strlen($this->right_icon) > 0 || strlen($this->right_image) > 0) {
        	$this->add('</span>');
        }
        $this->add($this->text);
		
	}
}
