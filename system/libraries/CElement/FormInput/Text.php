<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Text extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Text,
        CTrait_Element_Property_Placeholder;

    /**
     * @var string
     */
    protected $inputStyle;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $themeType = 'text';

    /**
     * @param null|string $id
     */
    public function __construct($id) {
        parent::__construct($id);

        $this->type = 'text';

        $this->placeholder = '';

        $this->inputStyle = 'default';

        $this->addClass('form-control');
    }

    /**
     * @param null|string $id
     *
     * @return \CElement_FormInput_Text
     */
    public static function factory($id = null) {
        return new CElement_FormInput_Text($id);
    }

    /**
     * Show an icon (eg. a Tabler `ti ti-mail` class) inside a Bootstrap
     * input-group prepended to this field. Not set (default) renders the
     * bare `<input>` exactly as before -- purely additive.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon) {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return void
     */
    protected function build() {
        parent::build();

        $this->setAttr('type', $this->type);
        $this->setAttr('placeholder', $this->placeholder);
        $this->addClass('input-unstyled');
        $validationClass = trim($this->validation->validationClass());
        if (strlen($validationClass) > 0) {
            $this->addClass($validationClass);
        }
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $inputHtml = parent::html($indent);

        if ($this->icon === null || strlen($this->icon) === 0) {
            return $inputHtml;
        }

        $html = new CStringBuilder();
        $html->setIndent($indent);
        $html->appendln('<div class="input-group">')
            ->incIndent()
            ->appendln('<span class="input-group-text"><i class="' . c::e($this->icon) . '"></i></span>')
            ->append($inputHtml)
            ->decIndent()
            ->appendln('</div>')
            ->br();

        return $html->text();
    }
}
