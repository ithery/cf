<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 2:29:43 AM
 */
class CElement_Component_Form_Field extends CElement_Component {
    use CTrait_Compat_Element_Form_Field;

    protected $group_classes = [];

    protected $group_id = '';

    protected $group_custom_css = [];

    protected $label = [];

    protected $show_label = [];

    protected $label_size = [];

    protected $fullwidth = [];

    protected $info_text = [];

    protected $label_class = [];

    protected $control_class = [];

    protected $style_form_group;

    protected $inline_without_default;

    protected $labelRequired = false;

    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'div';
        $this->label = '';
        $this->show_label = true;
        $this->label_size = 'medium';
        $this->info_text = '';
        $this->fullwidth = false;
        $this->group_id = '';
        $this->group_classes = [];
        $this->group_custom_css = [];
        $this->style_form_group = null;
        $this->inline_without_default = '0';
        $this->inline_without_default = carr::get($this->theme_style, 'inline_without_default');
        $this->labelRequired = false;
    }

    public static function factory($id = '') {
        return new CElement_Component_Form_Field($id);
    }

    public function setLabelRequired($bool = true) {
        $this->labelRequired = $bool;
        return $this;
    }

    public function toArray() {
        $data = [];

        $control_data = array_merge_recursive($data, parent::toArray());
        $data['attr']['class'] = 'control-group';
        $control_label = [];
        $control_label['tag'] = 'label';
        $control_label['attr']['class'] = 'control-label';
        $control_label['attr']['id'] = $this->id . '-label';
        $control_label['text'] = $this->label;

        $control_wrapper = [];
        if (isset($control_data['children'])) {
            $control_wrapper['children'] = $control_data['children'];
        }
        $control_wrapper['tag'] = 'div';

        $data['children'][] = $control_label;
        $data['children'][] = $control_wrapper;
        $data['tag'] = $this->tag;
        return $data;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);

        $group_classes = $this->group_classes;
        $group_classes = implode(' ', $group_classes);
        if (strlen($group_classes) > 0) {
            $group_classes = ' ' . $group_classes;
        }
        $group_custom_css = $this->group_custom_css;
        $group_custom_css = $this->renderStyle($group_custom_css);
        if (strlen($group_custom_css) > 0) {
            $group_custom_css = ' style="' . $group_custom_css . '"';
        }
        $group_attr = '';
        if (strlen($this->group_id) > 0) {
            $group_attr .= ' id="' . $this->group_id . '"';
        }

        $labelRequiredHtml = '';
        if ($this->labelRequired) {
            $labelRequiredHtml = '<span style="color: red;">*</span> ';
        }

        if ($this->bootstrap == '3.3') {
            // read default style from each theme
            if ($this->style_form_group == null || strlen($this->style_form_group) == 0) {
                $this->style_form_group = carr::get($this->theme_style, 'form_field_style');
            }
            $html->appendln('<div class="form-group ' . $group_classes . '" ' . $group_custom_css . $group_attr . '>')->incIndent();
            $label_class = '';
            $control_class = '';
            if ($this->style_form_group == 'inline') {
                if ($this->inline_without_default == '0') {
                    if (strlen($this->label_size)) {
                        $default_size = 12;
                        if (is_numeric($this->label_size)) {
                            $tmp_label_size = $this->label_size;
                            $tmp_control_size = $default_size - $this->label_size;

                            $this->label_class[] = 'col-md-' . $tmp_label_size;
                            $this->control_class[] = 'col-md-' . $tmp_control_size;
                        }
                    }
                    if (count($this->label_class) == 0) {
                        $this->label_class = ['col-md-2'];
                    }
                    if (count($this->control_class) == 0) {
                        $this->control_class = ['col-md-10'];
                    }
                    $this->label_class[] = 'control-label';
                }
            }

            $this->label_class[] = 'lbl-form-group';
            $label_class .= ' ' . implode(' ', $this->label_class);
            $control_class .= ' ' . implode(' ', $this->control_class);
            if ($this->show_label) {
                $html->appendln('<label class="form-label ' . $label_class . '" id="' . $this->id . '">' . $this->label . '</label>')->br();
            }
            if ($this->style_form_group == 'inline') {
                $html->appendln('<div class="' . $control_class . '">');
            }
            $html->appendln($this->htmlChild($html->getIndent()))->br();
            if ($this->style_form_group == 'inline') {
                $html->appendln('</div>');
            }
            $html->appendln('</div>');
        } else {
            $class_form_field = 'control-group';
            $label_class = '';
            $control_class = '';
            if ($this->bootstrap == '3') {
                $class_form_field = 'form-group';
                if ($this->style_form_group == 'inline') {
                    $class_form_field .= ' row';

                    if ($this->label_size != 'none') {
                        $label_class = 'col-md-3';
                        $control_class = 'col-md-9';
                        if ($this->label_size == 'large') {
                            $label_class = 'col-md-5';
                            $control_class = 'col-md-7';
                        } elseif ($this->label_size == 'small') {
                            $label_class = 'col-md-1';
                            $control_class = 'col-md-11';
                        } else {
                            $label_class = 'col-md-3';
                            $control_class = 'col-md-9';
                        }
                    }
                }
            }
            $label_class .= ' ' . implode(' ', $this->label_class);
            $control_class .= ' ' . implode(' ', $this->control_class);
            $html->appendln('<div class="' . $class_form_field . ' ' . $group_classes . '" ' . $group_custom_css . $group_attr . '>')->incIndent();
            if ($this->show_label) {
                $html->appendln('<label id="' . $this->id . '" class="form-label ' . $label_class . ' control-label">' . $labelRequiredHtml . $this->label . '</label>')->br();
            }
            $fullwidth = '';
            if ($this->fullwidth) {
                $fullwidth .= ' ' . 'full-width';
            }

            if ($this->bootstrap == '3') {
                if ($this->style_form_group == 'inline') {
                    $html->appendln('<div class="' . $control_class . '">')->incIndent()->br();
                }
            } else {
                $html->appendln('<div class="controls">')->incIndent()->br();
            }

            $html->appendln($this->htmlChild($html->getIndent()))->br();
            if (strlen($this->info_text) > 0) {
                $html->appendln('<p class="help-block">' . $this->info_text . '</p>')->incIndent()->br();
            }
            if ($this->bootstrap == '3') {
                if ($this->style_form_group == 'inline') {
                    $html->decIndent()->appendln('</div>')->incIndent()->br();
                }
            } else {
                $html->decIndent()->appendln('</div>')->incIndent()->br();
            }
            $html->appendln('<div style="clear:both"></div>')->incIndent()->br();
            $html->decIndent()->appendln('</div>');
        }
        /*
          if(isset($field["info_bubble"])) {
          $html.='<span class="info-spot">';
          $html.='<span class="icon-info-round"></span>';
          $html.='<span class="info-bubble">';
          $html.=$field["info_bubble"];
          $html.='</span>';
          $html.='</span>';
          }
         */
        return $html->text();
    }

    public function js($indent = 0) {
        $js = CStringBuilder::factory()->setIndent($indent);

        $js->setIndent($indent);

        $js->appendln(parent::js($js->getIndent()))->br();

        return $js->text();
    }

    public function setStyleFormGroup($style_form_group) {
        $this->style_form_group = $style_form_group;
        return $this;
    }

    public function setGroupId($id) {
        $this->group_id = $id;
        return $this;
    }

    public function addGroupClass($class) {
        $this->group_classes[] = $class;
        return $this;
    }

    public function groupCustomCss($key, $val) {
        $this->group_custom_css[$key] = $val;
        return $this;
    }

    public function setLabelSize($size) {
        if (in_array($size, ['small', 'medium', 'large', 'none']) || is_numeric($size)) {
            $this->label_size = $size;
        }
        return $this;
    }

    public function setInfoText($info_text) {
        $this->info_text = $info_text;
        return $this;
    }

    /**
     * @param string $text
     * @param bool   $lang
     *
     * @return $this
     */
    public function setLabel($text, $lang = true) {
        if ($lang) {
            $text = clang::__($text);
        }
        $this->label = $text;
        return $this;
    }

    public function showLabel() {
        $this->show_label = true;
        return $this;
    }

    public function hideLabel() {
        $this->show_label = false;
        return $this;
    }

    public function styleFormInline() {
        $this->style_form_group = 'inline';
        return $this;
    }

    public function addLabelClass($label_class) {
        $this->label_class[] = $label_class;
        return $this;
    }

    public function addControlClass($control_class) {
        $this->control_class[] = $control_class;
        return $this;
    }

    public function getInlineWithoutDefault() {
        return $this->inline_without_default;
    }

    public function setInlineWithoutDefault($inline_without_default) {
        $this->inline_without_default = $inline_without_default;
        return $this;
    }
}