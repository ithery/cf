<?php

/**
 * Base class for form input elements (text, select, checkbox, etc).
 */
class CElement_FormInput extends CElement_Element {
    use CTrait_Compat_Element_FormInput;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * Whether to submit the closest form on change/changeDate.
     *
     * @var bool
     */
    protected $submit_onchange;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var bool
     */
    protected $ajax;

    /**
     * @var array
     */
    protected $list;

    /**
     * @var CElement_Component_Form_FieldValidation
     */
    protected $validation;

    /**
     * @var bool
     */
    protected $disabled;

    /**
     * @var bool
     */
    protected $readonly;

    /**
     * @param string|null $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);

        $this->type = 'text';
        $this->tag = 'input';
        $this->name = $id;

        //sanitize name
        $this->id = str_replace('[', '', $this->id);
        $this->id = str_replace(']', '', $this->id);

        $this->submit_onchange = false;
        $this->ajax = false;
        $this->size = 'medium';
        $this->value = '';
        $this->disabled = '';
        $this->list = [];
        $this->validation = new CElement_Component_Form_FieldValidation();
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setSubmitOnChange($bool = true) {
        $this->submit_onchange = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setAjax($bool = true) {
        $this->ajax = $bool;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setDisabled($bool = true) {
        $this->disabled = $bool;

        return $this;
    }

    /**
     * @param string $size
     *
     * @return $this
     */
    public function setSize($size) {
        $this->size = $size;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setReadonly($bool = true) {
        $this->readonly = $bool;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldId() {
        return $this->id;
    }

    /**
     * @param mixed $val
     *
     * @return $this
     */
    public function setValue($val) {
        $this->value = $val;

        return $this;
    }

    /**
     * @param array $list
     *
     * @return $this
     */
    public function setList($list) {
        $this->list = $list;

        return $this;
    }

    /**
     * @param string $val
     *
     * @return $this
     */
    public function setName($val) {
        $this->name = $val;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function addValidation($name, $value = '') {
        $this->validation->addValidation($name, $value);

        return $this;
    }

    /**
     * Get the Laravel-style validation rules (eg. ['required', 'min:5']) declared
     * on this field via addValidation(), used by CElement_Component_Form to build
     * its Form::setValidation() rules array.
     *
     * @return array
     */
    public function getValidationRules() {
        return $this->validation->rules();
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        $data = [];
        if ($this->disabled) {
            $data['attr']['disabled'] = 'disabled';
        }
        if ($this->readonly) {
            $data['attr']['readonly'] = 'readonly';
        }
        if (strlen($this->name) > 0) {
            $data['attr']['name'] = $this->name;
        }
        $data = array_merge_recursive($data, parent::toArray());

        return $data;
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();

        if (!is_array($this->value)) {
            $this->setAttr('value', $this->value);
        }
        if ($this->readonly) {
            $this->setAttr('readonly', 'readonly');
        }
        if ($this->disabled) {
            $this->setAttr('disabled', 'disabled');
        }
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        $js = '';
        if ($this->submit_onchange) {
            if ($this->type == 'date') {
                $js .= "
                    $('#" . $this->id . "').on('changeDate',function() {
                        $(this).closest('form').submit();
                    });

                ";
            }
            $js .= "
                $('#" . $this->id . "').on('change',function() {
                    $(this).closest('form').submit();
                });

            ";
        }
        $js .= $this->jsChild($indent);

        return $js;
    }

    /**
     * @return string
     */
    protected function htmlAttr() {
        $htmlAttr = parent::htmlAttr();
        $nameAttr = ' name="' . c::e($this->name) . '"';

        return $htmlAttr . $nameAttr;
    }
}
