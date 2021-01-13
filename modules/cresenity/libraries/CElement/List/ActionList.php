<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_List_ActionList extends CElement_List {
    use CTrait_Compat_Element_ActionList,
        CTrait_Element_Property_Label,
        CTrait_Element_Property_Icon;

    public $actions = [];
    protected $style;
    protected $withCaret;

    public function __construct($listId = null) {
        parent::__construct($listId);

        $this->style = 'btn-list';
        $this->label = clang::__('Action');
        $this->btn_dropdown_classes = [];
        $this->label_size = 2;
        $this->icon = '';
        $this->withCaret = true;
    }

    public static function factory($list_id = '') {
        return new CElement_List_ActionList($list_id);
    }

    public function setStyle($style) {
        if (in_array($style, ['form-action', 'btn-group', 'btn-icon-group', 'btn-list', 'icon-segment', 'btn-dropdown', 'widget-action', 'table-header-action'])) {
            $this->style = $style;
        } else {
            trigger_error('style is not defined');
        }
        if ($this->id == 'test-123') {
            //echo($this->style);
        }
        return $this;
    }

    public function getStyle() {
        return $this->style;
    }

    public function removeCaret() {
        $this->withCaret = false;
        return $this;
    }

    protected function htmlCaret() {
        return $this->withCaret ? '<span class="caret"></span>' : '';
    }

    public function html($indent = 0) {
        if ($this->id == 'test-123') {
            //die($this->style);
        }
        //apply render style to child before render
        if (count($this->btn_dropdown_classes) == 0) {
            if ($this->bootstrap >= '3') {
                $this->btn_dropdown_classes[] = 'btn-primary';
                $this->btn_dropdown_classes[] = 'btn-sm';
            }
        }

        $this->apply('style', $this->style, 'CElement_Component_Action');
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $classes = $this->getNormalizedClasses();
        $ulDropdownClasses = '';
        if (!in_array('dropdown-menu-right', $classes)
            && !in_array('dropdown-menu-left', $classes)
        ) {
            $ulDropdownClasses .= ' dropdown-menu-right';
        }
        if (!in_array('pull-right', $classes)
            && !in_array('pull-left', $classes)
        ) {
            $ulDropdownClasses .= ' pull-right';
        }
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }
        $btn_dropdown_classes = $this->btn_dropdown_classes;
        $btn_dropdown_classes = implode(' ', $btn_dropdown_classes);
        if (strlen($btn_dropdown_classes) > 0) {
            $btn_dropdown_classes = ' ' . $btn_dropdown_classes;
        }
        $custom_css = $this->custom_css;
        $custom_css = static::renderStyle($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        $pretag = '<div id="' . $this->id . '" class="button-list ' . $classes . '">';
        switch ($this->style) {
            case 'form-action':
                if ($this->bootstrap == '3.3') {
                    $control_size = 12 - $this->label_size;
                    $pretag = '
                        <div class="form-group clear-both ' . $classes . '">
                            <label class="col-md-' . $this->label_size . ' control-label"></label>
                                <div class="col-md-' . $control_size . '">
                            ';
                } else {
                    $pretag = '<div class="form-actions clear-both ' . $classes . '">';
                }
                break;
            case 'btn-group':
            case 'btn-icon-group':
                $pretag = '<div class="btn-group ' . $classes . '">';
                break;
            case 'widget-action':
                $pretag = '<div class="buttons ' . $classes . '">';
                break;
            case 'table-header-action':
                $pretag = '<div class="buttons ' . $classes . '">';
                break;
            case 'btn-dropdown':
                $pretag = '<div id="' . $this->id . '" class="btn-group ' . $classes . '">';
                break;
        }
        $html->appendln($pretag)->incIndent()->br();
        if ($this->style == 'btn-dropdown') {
            $iconHtml = '';
            if (strlen($this->icon) > 0) {
                $iconHtml = '<i class="' . $this->icon . '"></i> ';
            }
            $caretClass = $this->withCaret ? '' : 'no-caret';
            $html->appendln('
                    <a class="btn ' . $btn_dropdown_classes . ' dropdown-toggle ' . $caretClass . '" data-toggle="dropdown" href="#">
                            ' . $iconHtml . $this->label . '
                            ' . $this->htmlCaret() . '
                    </a>
                    <ul class="dropdown-menu ' . $ulDropdownClasses . ' align-left ' . $classes . '">

            ');
        }

        $html->appendln($this->htmlChild($html->getIndent()));

        if ($this->style == 'btn-dropdown') {
            $html->appendln('</ul>');
        }
        $posttag = '</div>';
        switch ($this->style) {
            case 'btn-dropdown':
                $posttag = '</div>';
                break;
            case 'form-action':
                if ($this->bootstrap == '3.3') {
                    $posttag = '</div></div>';
                } else {
                    $posttag = '</div>';
                }
                break;
            default:
                $posttag = '</div>';
                break;
        }
        $html->decIndent()->appendln($posttag)->br();
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->appendln($this->jsChild($js->getIndent()));
        return $js->text();
    }
}
