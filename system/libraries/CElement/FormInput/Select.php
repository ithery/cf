<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Select extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Select;
    use CTrait_Element_Property_ApplyJs;
    use CTrait_Element_Property_DependsOn;
    use CTrait_Element_Property_Placeholder;

    /**
     * Options grouped into `<optgroup>` blocks, keyed by group label,
     * each value being a `value => label` list for that group.
     *
     * @var array
     */
    protected $groupList = [];

    /**
     * Whether this select allows multiple selected values.
     *
     * @var bool
     */
    protected $multiple;

    /**
     * Extra CSS classes applied to the rendered select2 dropdown panel
     * (select2's `dropdownCssClass` option).
     *
     * @var array
     */
    protected $dropdownClasses;

    /**
     * When true, hides the select2 search box by setting
     * `minimumResultsForSearch: Infinity`.
     *
     * @var bool
     */
    protected $hideSearch;

    /**
     * Maximum number of options that can be selected when `multiple` is
     * enabled. `false` means no limit.
     *
     * @var int|bool
     */
    protected $maximumSelectionLength;

    /**
     * Select2 library version to initialize with (eg. `'4'`).
     *
     * @var string
     */
    protected $select2Version;

    /**
     * Whether option labels/data-content should be rendered as HTML via
     * select2's `templateResult`/`templateSelection` callbacks.
     *
     * @var bool
     */
    protected $isOptionHtml;

    /**
     * Icon class (eg. a Tabler `ti ti-mail` class) rendered in a
     * Bootstrap input-group prepended to this field.
     *
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $themeType = 'select';

    /**
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->dropdownClasses = [];
        $this->tag = 'select';
        $this->multiple = false;
        $this->type = 'select';
        $this->placeholder = '';
        $this->applyJs = 'false';
        $this->hideSearch = false;
        $this->maximumSelectionLength = false;
        $this->select2Version = c::theme('select2.version');
        $this->isOptionHtml = false;
        $this->addClass('form-control form-select');
    }

    /**
     * @param string|null $id
     *
     * @return static
     */
    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    /**
     * Enable or disable multi-select mode.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setMultiple($bool = true) {
        $this->multiple = $bool;

        return $this;
    }

    /**
     * Set the maximum number of items that can be selected in a multi-select.
     *
     * @param int $length
     *
     * @return $this
     */
    public function setMaximumSelectionLength($length) {
        $this->maximumSelectionLength = $length;

        return $this;
    }

    /**
     * Set whether option labels/data-content should be treated as HTML.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setIsOptionHtml($bool = true) {
        $this->isOptionHtml = $bool;

        return $this;
    }

    /**
     * @return array
     */
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

    /**
     * @return void
     */
    protected function build() {
        parent::build();
        $this->addClass('form-control');
    }

    /**
     * @param int $indent
     *
     * @return string
     */
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
        if (count($this->groupList) > 0) {
            foreach ($this->groupList as $g => $list) {
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
        $selectHtml = $html->text();

        if ($this->icon !== null && strlen($this->icon) > 0) {
            $wrapped = new CStringBuilder();
            $wrapped->setIndent($indent);
            $wrapped->appendln('<div class="input-group">')
                ->incIndent()
                ->appendln('<span class="input-group-text"><i class="' . c::e($this->icon) . '"></i></span>')
                ->append($selectHtml)
                ->decIndent()
                ->appendln('</div>')
                ->br();

            return $wrapped->text();
        }

        return $selectHtml;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
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

            $dropdownClasses = $this->dropdownClasses;
            $dropdownClasses = implode(' ', $dropdownClasses);
            if (strlen($dropdownClasses) > 0) {
                $dropdownClasses = ' ' . $dropdownClasses;
            }
            $js->append("$('#" . $this->id . "').select2({
                        dropdownCssClass: '" . $dropdownClasses . "', // apply css that makes the dropdown taller
            ");
            if ($this->hideSearch) {
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

    /**
     * Set whether to hide the select2 search box.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setHideSearch($bool) {
        $this->hideSearch = $bool;

        return $this;
    }

    /**
     * Show an icon (eg. a Tabler `ti ti-mail` class) inside a Bootstrap
     * input-group prepended to this field. Not set (default) renders the
     * bare `<select>` exactly as before -- purely additive.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon) {
        $this->icon = $icon;

        return $this;
    }
}
