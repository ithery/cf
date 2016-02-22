<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CRenderable extends CObject implements IRenderable {

    protected $renderable;
    protected $additional_js;
	protected $visibility;
	protected $parent;

    protected function __construct($id = "") {
        parent::__construct($id);

        $this->renderable = array();

        $this->additional_js = "";
		$this->visibility = true;
		$this->parent = null;
    }

    public function child_count() {
        return count($this->renderable);
    }
	
	public function childs() {
		return $this->renderable;
	}
	
	public function set_parent($parent) {
		$this->parent = $parent;
		return $this;
	}
	
	public function set_visibility($bool) {
		$this->visibility=$bool;
	}	
	
    public function apply($key, $value, $class_name = '') {
        foreach ($this->renderable as $r) {

            if ($class_name == '' || $r->class_name() == $class_name) {
                if (method_exists($r, $key)) {
                    $r->$key($value);
                } else {
                    $r->$key = $value;
                }
            }
        }
        return $this;
    }

    public function add($renderable) {
		if (CRenderable::is_instanceof($renderable)) {
			$renderable->set_parent($this);
		}
        $this->renderable[] = $renderable;
        return $this;
    }

    public function add_js($js) {
        $this->additional_js.=$js;
        return $this;
    }

    public function clear() {
        foreach ($this->renderable as $r) {
            if (CRenderable::is_instanceof($r)) {
                $r->clear();
            }
        }

        $this->renderable = array();
        return $this;
    }

    public function html($indent = 0) {
        if(!$this->visibility) {
			return '';
		}
		$html = new CStringBuilder();
        $html->set_indent($indent);
        $html->inc_indent();
        foreach ($this->renderable as $r) {
            if (CRenderable::is_instanceof($r)) {
				if($r->visibility) {
					$html->append($r->html($html->get_indent()));
				}
            } else {
                if (is_object($r) || is_array($r)) {
                    $html->append(cdbg::var_dump($r, true));
                } else {
                    $html->append($r);
                }
            }
        }
        $html->dec_indent();
        return $html->text();
    }

    public function js($indent = 0) {
        if(!$this->visibility) {
			return '';
		}
		$js = new CStringBuilder();
        $js->set_indent($indent);
        foreach ($this->renderable as $r) {
            if (CRenderable::is_instanceof($r)) {
				$js->append($r->js($js->get_indent()));
				
            }
        }
        $js->append($this->additional_js);
        return $js->text();
    }
	
	public function css($indent = 0) {
        if(!$this->visibility) {
			return '';
		}
		$css = new CStringBuilder();
        $css->set_indent($indent);
        $css->inc_indent();
        foreach ($this->renderable as $r) {
            if (CRenderable::is_instanceof($r)) {
				if($r->visibility) {
					$html->append($r->css($html->get_indent()));
				}
            } else {
                if (is_object($r) || is_array($r)) {
                    $html->append(cdbg::var_dump($r, true));
                } else {
                    $html->append($r);
                }
            }
        }
        $html->dec_indent();
        return $html->text();
    }

    public function json() {
        $data = array();
        $data["html"] = cmsg::flash_all() . $this->html();
        $data["js"] = cbase64::encode($this->js());
		$data["js_require"] = CClientScript::instance()->url_js_file();
		$data["css_require"] = CClientScript::instance()->url_css_file();
		
        return cjson::encode($data);
    }

    public function regenerate_id($recursive = false) {
        parent::regenerate_id();
        if ($recursive) {
            foreach ($this->renderable as $r) {
                if (CRenderable::is_instanceof($r)) {
                    $r->regenerate_id($recursive);
                }
            }
        }
    }

    public static function is_instanceof($value) {
        if (is_object($value)) {
            return ($value instanceof CRenderable);
        }
        return false;
    }
	
	public function toarray(){
		$arrays = array();		
		foreach($this->renderable as $r) {			
			if(CRenderable::is_instanceof($r)) {	
				$arrays[] = $r->toarray();
			} else {	
				$arrays[] = $r;
			}		
		}		
		$data=array();
		if(!empty($arrays)) {
			$data["children"]=$arrays;
		}
		return $data;
	}

}