<?php

abstract class CElement extends CObservable {
    use CTrait_Compat_Element;

    /**
     * Class css for this element.
     *
     * @var array
     */
    protected $classes;

    /**
     * HTML tag name for this element.
     *
     * @var string
     */
    protected $tag;

    /**
     * HTML attributes for this element.
     *
     * @var array
     */
    protected $attr;

    /**
     * Custom css style for this element, keyed by property name.
     *
     * @var array
     */
    protected $custom_css;

    /**
     * @var mixed
     */
    protected $bootstrap;

    /**
     * @var CElement_PseudoElement|null
     */
    protected $before;

    /**
     * @var CElement_PseudoElement|null
     */
    protected $after;

    /**
     * @param string|null $id
     * @param string      $tag
     *
     * @return void
     */
    public function __construct($id = null, $tag = 'div') {
        parent::__construct($id);

        $this->classes = [];
        $this->attr = [];
        $this->custom_css = [];

        $this->tag = $tag;
    }

    /**
     * Set custom css style.
     *
     * @param string $key
     * @param string $val
     *
     * @return $this
     */
    public function customCss($key, $val) {
        $this->custom_css[$key] = $val;

        return $this;
    }

    /**
     * @param string $tag
     *
     * @return void
     */
    public function setTag($tag) {
        $this->tag = $tag;
    }

    /**
     * Add class attribute value for the element.
     *
     * @param string|array $classes
     *
     * @return $this
     */
    public function addClass($classes) {
        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->addClass($class);
            }
        }

        $classes = (string) $classes;
        $classes = c::collect(explode(' ', $classes))->filter()->all();

        $this->classes = carr::merge($this->classes, $classes);

        return $this;
    }

    /**
     * Remove class attribute value for the element.
     *
     * @param string|array $classes
     *
     * @return $this
     */
    public function removeClass($classes) {
        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->removeClass($class);
            }
        }
        $classes = (string) $classes;
        $classes = c::collect(explode(' ', $classes))->filter()->all();

        foreach ($classes as $class) {
            if (($key = array_search($class, $this->classes)) !== false) {
                unset($this->classes[$key]);
            }
        }

        return $this;
    }

    /**
     * Get class as string.
     *
     * @return array
     */
    public function getClasses() {
        return $this->classes;
    }

    /**
     * Remove attr.
     *
     * @param string $k
     *
     * @return $this
     */
    public function removeAttr($k) {
        if (isset($this->attr[$k])) {
            unset($this->attr[$k]);
        }

        return $this;
    }

    /**
     * Alias for removeAttr.
     *
     * @param string $k
     *
     * @return $this
     */
    public function deleteAttr($k) {
        return $this->removeAttr($k);
    }

    /**
     * Set attribute for element.
     *
     * @param string|array $k
     * @param string       $v
     *
     * @return $this
     */
    public function setAttr($k, $v = null) {
        if (is_array($k)) {
            return $this->setAttrFromArray($k);
        }
        if ($k == 'class') {
            return $this->addClass($v);
        }

        // if ($k == 'style') {
        //     foreach (explode(';', $v) as $attr) {
        //         if (strlen(trim($attr)) > 0) { // for missing semicolon on last element, which is legal
        //             list($name, $value) = explode(':', $attr);
        //             $this->customCss($name, $value);
        //         }
        //     }

        //     return $this;
        // }

        $this->attr[$k] = $v;

        return $this;
    }

    /**
     * Set attribute for element with array.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function setAttrFromArray($arr) {
        foreach ($arr as $k => $v) {
            $this->setAttr($k, $v);
        }

        return $this;
    }

    /**
     * Alias for setAttr.
     *
     * @param string|array $k
     * @param string       $v
     *
     * @return $this
     */
    public function addAttr($k, $v) {
        return $this->setAttr($k, $v);
    }

    /**
     * Get attribute value.
     *
     * @param string $k
     *
     * @return mixed|null
     */
    public function getAttr($k) {
        if (isset($this->attr[$k])) {
            return $this->attr[$k];
        }

        return null;
    }

    /**
     * Get opening tag of this element.
     *
     * @return string
     */
    public function pretag() {
        return '<' . $this->tag . '>';
    }

    /**
     * Get closing tag of this element.
     *
     * @return string
     */
    public function posttag() {
        return '</' . $this->tag . '>';
    }

    /**
     * @return array
     */
    public function toArray() {
        $data = parent::toArray();
        if (!empty($this->classes)) {
            $data['attr']['class'] = implode(' ', $this->classes);
        }
        $data['attr']['id'] = $this->id;

        $data['tag'] = $this->tag;

        return $data;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    protected function htmlChild($indent = 0) {
        return parent::html($indent);
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    protected function jsChild($indent = 0) {
        return parent::js($indent);
    }

    /**
     * @return string
     */
    public function __toString() {
        $return = '<h3> HTML </h3>'
                . '<pre>'
                . '<code>'
                . htmlspecialchars($this->html())
                . '</code>'
                . '</pre>';
        $return .= '<h3> JS </h3>'
                . '<pre>'
                . '<code>'
                . htmlspecialchars($this->js())
                . '</code>'
                . '</pre>';

        return $return;
    }

    /**
     * @return CElement_PseudoElement
     */
    public function before() {
        if ($this->before == null) {
            $this->before = CElement_PseudoElement::factory();
        }

        return $this->before;
    }

    /**
     * @return CElement_PseudoElement
     */
    public function after() {
        if ($this->after == null) {
            $this->after = CElement_PseudoElement::factory();
        }

        return $this->after;
    }
}
