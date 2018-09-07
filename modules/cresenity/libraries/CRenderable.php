<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CRenderable extends CObject implements CApp_Interface_Renderable {

    use CTrait_Compat_Renderable;

    protected $renderable;
    protected $additional_js;
    protected $visibility;
    protected $parent;
    protected $wrapper;

    protected function __construct($id = "") {
        parent::__construct($id);

        $this->renderable = new CCollection();
        $this->wrapper = $this;
        $this->additional_js = "";
        $this->visibility = true;
        $this->parent = null;
    }

    public function childCount() {
        return count($this->renderable);
    }

    public function childs() {
        return $this->renderable;
    }

    public function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }

    public function setVisibility($bool) {
        $this->visibility = $bool;
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
        if ($renderable instanceof CRenderable) {
            $renderable->setParent($this);
        }

        $this->wrapper->renderable[] = $renderable;

        $this->dispatchEvent(CApp_Event::createOnRenderableAddedListener($renderable));

        return $this;
    }

    public function addJs($js) {
        $this->additional_js .= $js;
        return $this;
    }

    public function clear() {
        foreach ($this->renderable as $r) {
            if ($r instanceof CRenderable) {
                $r->clear();
            }
        }
        foreach ($this->renderable as $r) {
            if ($r instanceof CObject) {
                CObserver::instance()->remove($r);
            }
        }
        $this->renderable = array();
        return $this;
    }

    public function html($indent = 0) {
        if (!$this->visibility) {
            return '';
        }
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $html->incIndent();
        foreach ($this->renderable as $r) {
            if ($r instanceof CRenderable) {
                if ($r->visibility) {
                    $html->append($r->html($html->getIndent()));
                }
            } else {
                if (is_object($r) || is_array($r)) {
                    $html->append(cdbg::var_dump($r, true));
                } else {
                    $html->append($r);
                }
            }
        }
        $html->decIndent();
        return $html->text();
    }

    public function js($indent = 0) {
        if (!$this->visibility) {
            return '';
        }
        $js = new CStringBuilder();
        $js->setIndent($indent);
        foreach ($this->renderable as $r) {
            if ($r instanceof CRenderable) {
                $js->append($r->js($js->getIndent()));
            }
        }
        $js->append($this->additional_js);
        return $js->text();
    }

    public function css($indent = 0) {
        if (!$this->visibility) {
            return '';
        }
        $css = new CStringBuilder();
        $css->set_indent($indent);
        $css->inc_indent();
        foreach ($this->renderable as $r) {
            if (CRenderable::is_instanceof($r)) {
                if ($r->visibility) {
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
        $data["js_require"] = CClientScript::instance()->urlJsFile();
        $data["css_require"] = CClientScript::instance()->urlCssFile();

        return cjson::encode($data);
    }

    public function regenerateId($recursive = false) {
        parent::regenerateId();
        if ($recursive) {
            foreach ($this->renderable as $r) {
                if ($r instanceof CRenderable) {
                    $r->regenerateId($recursive);
                }
            }
        }
    }

    public static function isInstanceof($value) {
        if (is_object($value)) {
            return ($value instanceof CRenderable);
        }
        return false;
    }

    public function toArray() {
        $arrays = array();
        foreach ($this->renderable as $r) {
            if ($r instanceof CRenderable) {
                $arrays[] = $r->toarray();
            } else {
                $arrays[] = $r;
            }
        }
        $data = array();
        if (!empty($arrays)) {
            $data["children"] = $arrays;
        }
        return $data;
    }

    /**
     * Fire the given event if possible.
     *
     * @param  mixed  $event
     * @return void
     */
    protected function dispatchEvent($event) {

        $this->getEvent()->dispatch($event);
    }

    /**
     * 
     * @return CApp_Event;
     */
    public function getEvent() {
        return CManager_Event::app();
    }

    /**
     * Register a renderable created listener with the CApp.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function listenOnRenderableAdded(Closure $callback) {
        $this->getEvent()->listen(CApp_Event::onRenderableAdded, $callback);
    }

    /**
     * Register custom event with the CApp.
     * 
     * @param string $event
     * @param Closure $callback
     */
    public function listen($event, Closure $callback) {
        $this->getEvent()->listen($event, $callback);
    }

}
