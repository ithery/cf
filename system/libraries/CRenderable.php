<?php

use Illuminate\Contracts\Support\Renderable;

defined('SYSPATH') or die('No direct access allowed.');

class CRenderable extends CObject implements CApp_Interface_Renderable {
    use CTrait_Compat_Renderable;

    /**
     * Renderable Child Array.
     *
     * @var CRenderable[]
     */
    protected $renderable;

    /**
     * Extra raw JS appended after children's own js() in js(), see addJs().
     *
     * @var string
     */
    protected $additionalJs;

    /**
     * Whether html()/js() render anything at all for this node (and, while
     * iterating a parent's children, whether it's skipped there too).
     *
     * @var bool
     */
    protected $visibility;

    /**
     * This node's parent, set via setParent()/add(); null for a root node
     * or after detach().
     *
     * @var null|CRenderable
     */
    protected $parent;

    /**
     * The renderable that add() actually appends children to -- itself by
     * default, but subclasses (e.g. widgets wrapping their content in an
     * inner div) may point this at a descendant instead.
     *
     * @var CRenderable
     */
    protected $wrapper;

    /**
     * @param null|string $id
     */
    protected function __construct($id = null) {
        parent::__construct($id);

        $this->renderable = new CCollection();
        $this->wrapper = $this;
        $this->additionalJs = '';
        $this->visibility = true;
        $this->parent = null;
    }

    /**
     * @return int
     */
    public function childCount() {
        return count($this->renderable);
    }

    /**
     * @return CCollection|CRenderable[]
     */
    public function childs() {
        return $this->renderable;
    }

    /**
     * @param CRenderable $parent
     *
     * @return $this
     */
    public function setParent($parent) {
        $this->parent = &$parent;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setVisibility($bool) {
        $this->visibility = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible() {
        return $this->visibility;
    }

    /**
     * Apply call method or set property of all childs of this object.
     *
     * @param string            $key
     * @param mixed             $value
     * @param null|string|array $className
     *
     * @return $this
     */
    public function apply($key, $value, $className = null) {
        if ($className !== null) {
            $className = carr::wrap($className);
        }
        foreach ($this->renderable as $r) {
            if ($className === null || in_array($r->className(), $className)) {
                if (method_exists($r, $key)) {
                    $r->$key($value);
                } else {
                    $r->$key = $value;
                }
            }
        }

        return $this;
    }

    /**
     * @param mixed $renderable
     *
     * @return $this
     */
    public function add($renderable) {
        if ($renderable instanceof CRenderable) {
            $renderable->setParent($this);
        }

        $this->wrapper->renderable[] = $renderable;

        $this->dispatchEvent(CApp_Event::createEventOnRenderableAdded($renderable));

        return $this;
    }

    /**
     * @param string $js
     *
     * @return $this
     */
    public function addJs($js) {
        $this->additionalJs .= $js;

        return $this;
    }

    /**
     * @return $this
     */
    public function clear() {
        foreach ($this->renderable as $r) {
            if ($r instanceof CRenderable) {
                $r->clear();
            }
            if ($r instanceof CObject) {
                CObserver::instance()->remove($r);
            }
        }
        $this->renderable = [];

        return $this;
    }

    /**
     * @return string
     */
    public function parentHtml() {
        return parent::html();
    }

    /**
     * @return string
     */
    public function parentJs() {
        return parent::js();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        if (!$this->visibility) {
            return '';
        }
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $html->incIndent();
        foreach ($this->renderable as $r) {
            $child = null;

            if ($r instanceof CRenderable) {
                if (!$r->visibility) {
                    continue;
                }

                $r = $r->html($html->getIndent());
            }
            if ($r instanceof Renderable) {
                $r = $r->render();
            }

            /**
             * \Stringable available on PHP 8.
             */
            if ($r instanceof \Stringable) {
                $r = $r->__toString();
            }

            if (is_object($r) || is_array($r)) {
                $dumper = new CDebug_Dumper();
                $r = $dumper->getDump($r);
            }

            $html->append($r);
        }
        $html->decIndent();

        return $html->text();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
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
        $js->append($this->additionalJs);

        return $js->text();
    }

    /**
     * @return string json-encoded {html, js (base64), js_require, css_require}
     */
    public function json() {
        $data = [];
        $data['html'] = cmsg::flash_all() . $this->html();
        $data['js'] = base64_encode($this->js());
        $data['js_require'] = CManager::asset()->getAllJsFileUrl();
        $data['css_require'] = CManager::asset()->getAllCssFileUrl();

        return json_encode($data);
    }

    /**
     * @param bool $recursive
     *
     * @return void
     */
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

    /**
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        $data['visibility'] = $this->visibility;
        foreach ($this->renderable as $r) {
            if ($r instanceof CRenderable) {
                $arrays[] = $r->toArray();
            } else {
                $arrays[] = $r;
            }
        }

        if (!empty($arrays)) {
            $data['children'] = $arrays;
        }

        return $data;
    }

    /**
     * Fire the given event if possible.
     *
     * @param mixed $event
     *
     * @return void
     */
    protected function dispatchEvent($event) {
        $this->getEvent()->dispatch($event);
    }

    /**
     * @return CEvent_Dispatcher
     */
    public function getEvent() {
        return CEvent::dispatcher();
    }

    /**
     * Register a renderable created listener with the CApp.
     *
     * @param \Closure $callback
     *
     * @deprecated 1.2
     *
     * @return void
     */
    public function listenOnRenderableAdded(Closure $callback) {
        $this->getEvent()->listen(CApp_Event_OnRenderableAdded::class, $callback);
    }

    /**
     * Register custom event with the CApp.
     *
     * @param string  $event
     * @param Closure $callback
     *
     * @return void
     */
    public function listen($event, Closure $callback) {
        $this->getEvent()->listen($event, $callback);
    }

    /**
     * @return null|CRenderable
     */
    public function &getParent() {
        return $this->parent;
    }

    /**
     * @param array $styles map of CSS property => value
     *
     * @return string inline style attribute value, e.g. 'color:red;width:10px;'
     */
    public static function renderStyle(array $styles) {
        if ($styles == null) {
            return '';
        }
        $ret = '';
        foreach ($styles as $k => $v) {
            $ret .= $k . ':' . $v . ';';
        }

        return $ret;
    }

    /**
     * @return $this
     */
    public function detach() {
        $this->parent = null;

        return $this;
    }
}
