<?php

class CElement_FormInput_Textarea extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Textarea;
    use CTrait_Element_Property_Placeholder;

    protected $col;

    protected $row;

    public function __construct($id) {
        parent::__construct($id);

        $this->tag = 'textarea';
        $this->isOneTag = false;

        $this->col = 60;
        $this->row = 10;

        $this->addClass('form-control');
    }

    public function build() {
        parent::build();
        if ($this->readonly) {
            $this->setAttr('readonly', 'readonly');
        }
        if ($this->disabled) {
            $this->setAttr('disabled', 'disabled');
        }
        if ($this->row) {
            $this->setAttr('row', $this->row);
        }
        if ($this->col) {
            $this->setAttr('col', $this->col);
        }
        if (strlen($this->placeholder) > 0) {
            $this->setAttr('placeholder', $this->placeholder);
        }
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->setIndent($indent);
        $this->buildOnce();
        $html->appendln($this->beforeHtml($indent));

        $html->append($this->pretag());
        $html->append($this->value);
        $html->append($this->posttag());

        $html->appendln($this->afterHtml($indent));

        return $html->text();
    }

    public function setCol($col) {
        $this->col = $col;

        return $this;
    }

    public function setRow($row) {
        $this->row = $row;

        return $this;
    }
}
