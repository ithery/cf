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
    use CTrait_Element_Property_Label;
    use CTrait_Element_Property_Tooltip;

    protected $groupClasses = [];

    protected $group_id = '';

    protected $group_custom_css = [];

    protected $show_label = [];

    protected $label_size = [];

    protected $fullwidth = [];

    protected $infoText = '';

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
        $this->infoText = '';
        $this->fullwidth = false;
        $this->group_id = '';
        $this->groupClasses = [];
        $this->group_custom_css = [];
        $this->style_form_group = null;
        $this->inline_without_default = 'inline_without_default';
        $this->labelRequired = false;
    }

    public static function factory($id = null) {
        // @phpstan-ignore-next-line
        return new static($id);
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

        $labelRequiredHtml = '';
        if ($this->labelRequired) {
            $labelRequiredHtml = '<span style="color: red;">*</span> ';
        }

        $classFormField = 'control-group form-group';
        $label_class = '';
        $control_class = '';

        $groupIdAttr = '';
        if (strlen($this->group_id) > 0) {
            $groupIdAttr = 'id="' . $this->group_id . '" ';
        }

        $label_class .= ' ' . implode(' ', $this->label_class);
        $control_class .= ' ' . implode(' ', $this->control_class);
        $html->appendln('<div ' . $groupIdAttr . ' class="' . $classFormField . ' ' . $classes . '" ' . $custom_css . $addition_attribute . '>')->incIndent();
        if ($this->show_label) {
            if ($this->tooltip) {
                $this->tooltip->addClass('ml-1');
            }
            $tooltipHtml = $this->tooltip ? $this->tooltip->html() : '';
            $html->appendln('<label id="' . $this->id . '" class="form-label ' . $label_class . ' control-label">' . $labelRequiredHtml . $this->label . $tooltipHtml . '</label>')->br();
        }

        $html->appendln('<div class="controls">')->incIndent()->br();

        $html->appendln($this->htmlChild($html->getIndent()))->br();
        if (strlen($this->infoText) > 0) {
            $html->appendln('<small class="help-block">' . $this->infoText . '</small>')->incIndent()->br();
        }

        $html->decIndent()->appendln('</div>')->incIndent()->br();

        $html->appendln('<div style="clear:both"></div>')->incIndent()->br();
        $html->decIndent()->appendln('</div>');

        return $html->text();
    }

    public function js($indent = 0) {
        $js = CStringBuilder::factory()->setIndent($indent);

        $js->setIndent($indent);
        $tooltipJs = $this->tooltip ? $this->tooltip->js() : '';

        if ($tooltipJs) {
            $js->appendln($tooltipJs);
        }

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
        $this->groupClasses[] = $class;

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

    public function setInfoText($infoText) {
        $this->infoText = $infoText;

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
