<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 12, 2017, 3:34:27 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CElement_Element extends CElement {

    protected $before;
    protected $after;
    protected $isBuilded = false;
    protected $isOneTag = false;
    protected $haveIndent = true;
    protected $is_show = true;
    private $isBuild = false;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id);

        $this->theme = CManager::theme()->getCurrentTheme();

        $this->before = null;
        $this->after = null;

        $this->isBuilded = false;
        $this->isOneTag = false;



        $this->bootstrap = ccfg::get('bootstrap');
        if (strlen($this->bootstrap) == 0) {
            $this->bootstrap = '2';
        }
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
     * 
     * @param bool $bool
     * @return $this
     */
    public function setHaveIndent($bool = true) {
        $this->haveIndent = $bool;
        return $this;
    }

    protected function htmlAttr() {
        


        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = "";
        $haveClass = false;
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= " " . $k . '="' . $v . '"';
            if($k=="class") {
                $haveClass=true;
            }
        }
        $classAttr = "";
        if(!$haveClass) {
            $classes = $this->classes;
            $classes = implode(" ", $classes);
            $classAttr = ' class="' . $classes . '"';
        }
        $html_attr = 'id="' . $this->id . '" ' . $classAttr . $custom_css . $addition_attribute;
        return $html_attr;
    }

    public static function is_instanceof($val) {
        if (is_object($val)) {
            return ($val instanceof CElement_Element);
        }
        return false;
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

    public function before() {
        if ($this->before == null) {
            $this->before = CElement_PseudoElement::factory();
        }
        return $this->before;
    }

    public function after() {
        if ($this->after == null) {
            $this->after = CElement_PseudoElement::factory();
        }
        return $this->after;
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
            if ($this->is_show) {
                $html->$appendMethod($this->pretag());
                if ($this->haveIndent) {
                    $html->br();
                }
                if ($this->haveIndent) {
                    $html->incIndent();
                }
            }

            $html->$appendMethod($this->htmlChild($html->getIndent()));
            if ($this->haveIndent) {
                $html->br();
            }
            if ($this->is_show) {
                if ($this->haveIndent) {
                    $html->decIndent();
                }
                $html->$appendMethod($this->posttag());
                if ($this->haveIndent) {
                    $html->br();
                }
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
