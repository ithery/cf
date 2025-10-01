<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_SelectTag extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_SelectTag;

    protected $multiple;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->multiple = true;
    }

    public function html($indent = 0) {
        if (CManager::instance()->isRegisteredModule('bootstrap-4-material') || CManager::instance()->isRegisteredModule('bootstrap-4')) {
            $html = new CStringBuilder();
            $html->setIndent($indent);
            $readonly = '';
            if ($this->readonly) {
                $readonly = ' readonly="readonly"';
            }
            $disabled = '';
            if ($this->disabled) {
                $disabled = ' disabled="disabled"';
            }
            $multiple = '';
            if ($this->multiple) {
                $multiple = ' multiple="multiple"';
            }
            $name = $this->name;
            if ($this->multiple) {
                $name = $name . '[]';
            }
            $classes = $this->classes;
            $classes = implode(' ', $classes);
            if (strlen($classes) > 0) {
                $classes = ' ' . $classes;
            }

            $custom_css = $this->custom_css;
            $custom_css = $this->renderStyle($custom_css);
            if (strlen($custom_css) > 0) {
                $custom_css = ' style="' . $custom_css . '"';
            }
            $addition_attribute = '';
            foreach ($this->attr as $k => $v) {
                $addition_attribute .= ' ' . $k . '="' . $v . '"';
            }
            $html->appendln('<select name="' . $name . '" id="' . $this->id . '" class="form-control select' . $classes . $this->validation->validationClass() . '"' . $custom_css . $disabled . $readonly . $multiple . $addition_attribute . '>')
                ->incIndent()->br();

            if ($this->list != null) {
                foreach ($this->list as $k => $v) {
                    $selected = '';
                    if (is_array($this->value)) {
                        if (in_array($k, $this->value)) {
                            $selected = ' selected="selected"';
                        }
                    } else {
                        if ($this->value == (string) $k) {
                            $selected = ' selected="selected"';
                        }
                    }
                    $value = $v;
                    $addition_attribute = ' ';
                    if (is_array($v)) {
                        $value = carr::get($v, 'value');
                        $attributes = carr::get($v, 'attributes', []);
                        foreach ($attributes as $attribute_k => $attribute_v) {
                            $addition_attribute .= ' ' . $attribute_k . '="' . $attribute_v . '"';
                        }
                    }
                    if ($this->readonly) {
                        if ($k == $this->value) {
                            $html->appendln('<option value="' . $k . '" ' . $selected . $addition_attribute . '>' . $value . '</option>')->br();
                        }
                    } else {
                        $html->appendln('<option value="' . $k . '" ' . $selected . $addition_attribute . '>' . $value . '</option>')->br();
                    }
                }
            }
            $html->decIndent()->appendln('</select>')->br();

            //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
            return $html->text();
        }
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $html->append($this->htmlChild($indent));

        $custom_css = $this->renderStyle($this->custom_css);
        $multiple = ' multiple="multiple"';
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }

        $classes = implode(' ', $this->classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }

        $html->appendln('<input type="hidden"  class="' . $classes . '" name="' . $this->name . '" id="' . $this->id . '" ' . $custom_css . '/>')->br();

        return $html->text();
    }

    public function js($indent = 0) {
        if (CManager::instance()->isRegisteredModule('bootstrap-4-material') || CManager::instance()->isRegisteredModule('bootstrap-4')) {
            $js = "
                $('#" . $this->id . "').select2({
                    tags: true,
                    tokenSeparators: [',', ' ']
                }).change(function() {

                });

            ";

            return $js;
        }
        $vals = $this->value;
        if (!is_array($vals)) {
            $vals = [$vals];
        }
        $vals_str = '';
        foreach ($vals as $val) {
            if (strlen($vals_str) > 0) {
                $vals_str .= ',';
            }
            $vals_str .= "'" . $val . "'";
        }

        $list = $this->list;
        if (!is_array($list)) {
            $list = [$list];
        }
        $list_str = '';
        foreach ($list as $val) {
            if (strlen($list_str) > 0) {
                $list_str .= ',';
            }
            $list_str .= "'" . $val . "'";
        }

        $str = "
			$('#" . $this->id . "').select2({
				tags: [" . $list_str . "],
				tokenSeparators: [',', ' ']
			}).change(function() {

			});

	    ";

        if (strlen($vals_str) > 0) {
            $str .= "
                $('#" . $this->id . "').select2('val',[" . $vals_str . ']);
            ';
        }

        $js = new CStringBuilder();
        $js->append(parent::js($indent))->br();
        $js->setIndent($indent);
        //echo $str;
        $js->append($str)->br();

        return $js->text();
    }
}
