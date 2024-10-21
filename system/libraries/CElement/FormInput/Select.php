<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2018, 2:52:36 PM
 */
class CElement_FormInput_Select extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Select;
    use CTrait_Element_Property_ApplyJs;
    use CTrait_Element_Property_DependsOn;
    use CTrait_Element_Property_Placeholder;

    protected $group_list = [];

    protected $multiple;

    protected $dropdown_classes;

    protected $hide_search;

    protected $maximumSelectionLength;

    protected $select2Version;

    protected $isOptionHtml;

    public function __construct($id) {
        parent::__construct($id);

        $this->dropdown_classes = [];
        $this->tag = 'select';
        $this->multiple = false;
        $this->type = 'select';
        $this->placeholder = '';
        $this->applyJs = 'false';
        $this->hide_search = false;
        $this->maximumSelectionLength = false;
        $this->select2Version = c::theme('select2.version');
        $this->isOptionHtml = false;
        $this->addClass('form-control select');
    }

    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    public function setMultiple($bool = true) {
        $this->multiple = $bool;

        return $this;
    }

    public function setMaximumSelectionLength($length) {
        $this->maximumSelectionLength = $length;

        return $this;
    }

    public function setIsOptionHtml($bool = true) {
        $this->isOptionHtml = $bool;

        return $this;
    }

    public function toArray() {
        $data = [];
        $data = array_merge_recursive($data, parent::toArray());
        if ($this->multiple) {
            $data['attr']['multiple'] = 'multiple';
        }
        $data['children'] = [];

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
                $child = [];
                $child['tag'] = 'option';
                $child['attr']['value'] = $k;
                if (strlen($selected) > 0) {
                    $child['attr']['selected'] = 'selected';
                }
                $child['text'] = $v;
                $data['children'][] = $child;
            }
        }

        return $data;
    }

    protected function build() {
        parent::build();
        $this->addClass('form-control');
    }

    public function html($indent = 0) {
        parent::html($indent);
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
        $html
            ->appendln('<select name="' . $name . '" id="' . $this->id . '" class="' . $classes . $this->validation->validationClass() . '"' . $custom_css . $disabled . $readonly . $multiple . $addition_attribute . '>')
            ->incIndent()
            ->br();
        if (count($this->group_list) > 0) {
            foreach ($this->group_list as $g => $list) {
                if (strlen($g) > 0) {
                    $html->appendln('<optgroup label="' . $g . '">')->br();
                }
                foreach ($list as $k => $v) {
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
                    $html->appendln('<option data-content="' . c::e($v) . '" value="' . $k . '"' . $selected . '>' . $v . '</option>')->br();
                }
                if (strlen($g) > 0) {
                    $html->appendln('</optgroup>')->br();
                }
            }
        }
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
                        $html->appendln('<option data-content="' . c::e($v) . '" value="' . $k . '" ' . $selected . $addition_attribute . '>' . $value . '</option>')->br();
                    }
                } else {
                    $html->appendln('<option data-content="' . c::e($v) . '" value="' . $k . '" ' . $selected . $addition_attribute . '>' . $value . '</option>')->br();
                }
            }
        }
        $html->decIndent()->appendln('</select>')->br();

        //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();
        $placeholder = '';
        if (strlen($this->placeholder) > 0) {
            $placeholder = $this->placeholder;
        }
        if ($this->applyJs == 'select2') {
            if ($this->select2Version == '4') {
                CManager::instance()->registerModule('select2-4.0');
            } else {
                CManager::instance()->registerModule('select2');
            }
            $classes = $this->classes;
            $classes = implode(' ', $classes);
            if (strlen($classes) > 0) {
                $classes = ' ' . $classes;
            }

            $dropdown_classes = $this->dropdown_classes;
            $dropdown_classes = implode(' ', $dropdown_classes);
            if (strlen($dropdown_classes) > 0) {
                $dropdown_classes = ' ' . $dropdown_classes;
            }
            $js->append("$('#" . $this->id . "').select2({
                        dropdownCssClass: '" . $dropdown_classes . "', // apply css that makes the dropdown taller
            ");
            if ($this->hide_search) {
                $js->append('minimumResultsForSearch: Infinity,');
            }
            if ($this->maximumSelectionLength !== false) {
                $js->append('maximumSelectionLength: ' . $this->maximumSelectionLength . ',');
            }

            if ($this->isOptionHtml) {
                $js->append("templateResult: function(state){
                    var dataContent = $(state.element).attr('data-content');
                    if(dataContent) {
                        return $(dataContent);
                    }

                    return state.text;

                 },");

                $js->append("templateSelection: function(state){
                    var dataContent = $(state.element).attr('data-content');
                    if(dataContent) {
                        return $(dataContent);
                    }

                    return state.text;

                 },");
            }

            $js->append("containerCssClass : 'tpx-select2-container " . $classes . "',");
            $js->append("placeholder : '" . $placeholder . "'");
            $js->append('});')->br();
        }
        if ($this->applyJs == 'chosen') {
            $js->append("$('#" . $this->id . "').chosen();")->br();
        }
        if ($this->applyJs == 'dualselect') {
            $js->append("$('#" . $this->id . "').multiSelect();")->br();
        }

        foreach ($this->dependsOn as $dependOn) {
            //we create ajax method

            $dependsOnSelector = $dependOn->getSelector()->getQuerySelector();
            $targetSelector = '#' . $this->id();
            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('DependsOn');
            $ajaxMethod->setMethod('post');
            $ajaxMethod->setData('dependsOn', serialize($dependOn));
            $ajaxMethod->setData('from', static::class);
            $ajaxUrl = $ajaxMethod->makeUrl();
            $throttle = $dependOn->getThrottle();
            $optionsJson = '{';
            $optionsJson .= "url:'" . $ajaxUrl . "',";
            $optionsJson .= "method:'" . 'post' . "',";
            $optionsJson .= !$dependOn->getBlock() ? 'block: false,' : '';
            $valueScript = $dependOn->getSelector()->getScriptForValue();
            $optionsJson .= 'dataAddition: { value: ' . $valueScript . ' },';
            $optionsJson .= "onSuccess: (data) => {
                 let jQuerySelect = $('" . $targetSelector . "');
                 jQuerySelect.empty();
                 let beforeValue = '" . $this->value . "';
                 data.forEach((item,index)=>{
                     let newOption = new Option(item.value,item.key);
                     if(beforeValue==item.key) {
                         newOption.selected='selected';
                     }
                     jQuerySelect.append(newOption);
                 });
            },";
            $optionsJson .= 'handleJsonResponse: true';
            $optionsJson .= '}';

            $optionsJson = str_replace(["\r\n", "\n", "\r"], '', $optionsJson);

            $dependsOnFunctionName = 'dependsOnFunction' . uniqid();
            $js->appendln('
                 let ' . $dependsOnFunctionName . ' = () => {
                     cresenity.ajax(' . $optionsJson . ");
                 };
                 $('" . $dependsOnSelector . "').change(cresenity.debounce(" . $dependsOnFunctionName . ' ,' . $throttle . '));
                 ' . $dependsOnFunctionName . '();
             ');
        }

        return $js->text();
    }

    public function setHideSearch($bool) {
        $this->hide_search = $bool;

        return $this;
    }
}
