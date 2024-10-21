<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Nov 12, 2017, 3:34:27 AM
 */
abstract class CElement_Element extends CElement {
    protected $isBuilded = false;

    protected $isOneTag = false;

    protected $haveIndent = true;

    public function __construct($id = null, $tag = 'div') {
        parent::__construct($id, $tag);

        $this->isBuilded = false;
        $this->isOneTag = false;
    }

    public function onetag() {
        return '<' . $this->tag . ' ' . $this->htmlAttr() . ' />';
    }

    public function pretag() {
        return '<' . $this->tag . ' ' . $this->htmlAttr() . ' >';
    }

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

    protected function buildOnce() {
        //just build once
        if (!$this->isBuilded) {
            $this->build();
            $this->isBuilded = true;
        }
    }

    public function beforeHtml($indent = 0) {
        return $this->before()->html($indent);
    }

    public function afterHtml($indent = 0) {
        return $this->after()->html($indent);
    }

    public function beforeJs($indent = 0) {
        return $this->before()->js($indent);
    }

    public function afterJs($indent = 0) {
        return $this->after()->js($indent);
    }

    protected function build() {
    }

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
