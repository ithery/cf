<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Base class for elements that render as a single HTML tag (with optional children),
 * as opposed to CElement_View/CElement_Template which render from a view/template.
 */
abstract class CElement_Element extends CElement {
    /**
     * Whether build() has already run for this instance, guarded by buildOnce().
     *
     * @var bool
     */
    protected $isBuilded = false;

    /**
     * Whether the tag is self-closing (rendered via onetag() instead of pretag()/posttag()).
     *
     * @var bool
     */
    protected $isOneTag = false;

    /**
     * Whether rendered HTML/JS should be indented.
     *
     * @var bool
     */
    protected $haveIndent = true;

    /**
     * @param string|null $id
     * @param string      $tag
     *
     * @return void
     */
    public function __construct($id = null, $tag = 'div') {
        parent::__construct($id, $tag);

        $this->isBuilded = false;
        $this->isOneTag = false;
    }

    /**
     * Get self-closing tag markup (eg. `<img ... />`).
     *
     * @return string
     */
    public function onetag() {
        return '<' . $this->tag . ' ' . $this->htmlAttr() . ' />';
    }

    /**
     * Get opening tag markup.
     *
     * @return string
     */
    public function pretag() {
        return '<' . $this->tag . ' ' . $this->htmlAttr() . ' >';
    }

    /**
     * Get closing tag markup.
     *
     * @return string
     */
    public function posttag() {
        return '</' . $this->tag . '>';
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setHaveIndent($bool = true) {
        $this->haveIndent = $bool;

        return $this;
    }

    /**
     * Build the `id`/`class`/`style` and additional HTML attribute string for this element's tag.
     *
     * @return string
     */
    protected function htmlAttr() {
        $customCss = $this->custom_css;
        $customCss = static::renderStyle($customCss);
        if (strlen($customCss) > 0) {
            $customCss = ' style="' . c::e($customCss) . '"';
        }
        $additionAttribute = '';
        $haveClass = false;
        foreach ($this->attr as $k => $v) {
            if (is_array($v)) {
                $v = implode(',', $v);
            }
            $additionAttribute .= ' ' . $k . '="' . c::e($v) . '"';
            if ($k == 'class') {
                $haveClass = true;
            }
        }
        $classAttr = '';
        if (!$haveClass) {
            $classes = $this->classes;
            $classes = implode(' ', $classes);
            $classAttr = ' class="' . c::e($classes) . '"';
        }
        $htmlAttr = 'id="' . $this->id . '" ' . $classAttr . $customCss . $additionAttribute;

        return $htmlAttr;
    }

    /**
     * Run build() exactly once per instance, memoized via $isBuilded.
     *
     * @return void
     */
    protected function buildOnce() {
        //just build once
        if (!$this->isBuilded) {
            $this->build();
            $this->isBuilded = true;
        }
    }

    /**
     * Get the rendered HTML of the "before" pseudo-element.
     *
     * @param int $indent
     *
     * @return string
     */
    public function beforeHtml($indent = 0) {
        return $this->before()->html($indent);
    }

    /**
     * Get the rendered HTML of the "after" pseudo-element.
     *
     * @param int $indent
     *
     * @return string
     */
    public function afterHtml($indent = 0) {
        return $this->after()->html($indent);
    }

    /**
     * Get the rendered JS of the "before" pseudo-element.
     *
     * @param int $indent
     *
     * @return string
     */
    public function beforeJs($indent = 0) {
        return $this->before()->js($indent);
    }

    /**
     * Get the rendered JS of the "after" pseudo-element.
     *
     * @param int $indent
     *
     * @return string
     */
    public function afterJs($indent = 0) {
        return $this->after()->js($indent);
    }

    /**
     * Hook for subclasses to prepare attributes/state before rendering. Called once via buildOnce().
     *
     * @return void
     */
    protected function build() {
    }

    /**
     * Render this element (and its children) to an HTML string.
     *
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $html = new CStringBuilder();

        if (!$this->haveIndent) {
            $indent = 0;
        }
        $html->setIndent($indent);

        $this->buildOnce();
        $appendMethod = $this->haveIndent ? 'appendln' : 'append';
        $html->appendln($this->beforeHtml($indent));
        if ($this->isOneTag) {
            $html->$appendMethod($this->onetag());
        } else {
            $html->$appendMethod($this->pretag());
            if ($this->haveIndent) {
                $html->br();
            }
            if ($this->haveIndent) {
                $html->incIndent();
            }

            $html->$appendMethod($this->htmlChild($html->getIndent()));
            if ($this->haveIndent) {
                $html->br();
            }

            if ($this->haveIndent) {
                $html->decIndent();
            }
            $html->$appendMethod($this->posttag());
            if ($this->haveIndent) {
                $html->br();
            }
        }
        $html->$appendMethod($this->afterHtml($indent));

        return $html->text();
    }

    /**
     * Render this element's (and its children's) JS to a string.
     *
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $this->buildOnce();

        $js->appendln($this->beforeJs($js->getIndent()));
        $js->appendln($this->jsChild($js->getIndent()))->br();
        $js->appendln($this->afterJs($js->getIndent()));

        return $js->text();
    }
}
