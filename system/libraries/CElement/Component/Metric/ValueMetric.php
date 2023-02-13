<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Component_Metric_ValueMetric extends CElement_Component {
    use CElement_Trait_UseViewTrait;
    use CTrait_Element_Property_Icon;
    use CTrait_Element_Property_Label;
    use CTrait_Element_Property_Value;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->view = 'cresenity/element/component/metric/value';
    }

    public function setValueFromModel($model, $callback) {
    }

    protected function build() {
        if ($this->icon) {
            $this->viewData['icon'] = $this->icon;
        }
        if ($this->label) {
            $this->viewData['label'] = $this->label;
        }
        if ($this->value) {
            $value = $this->value;
            if (c::isCallable($value)) {
                $value = c::call($value);
            }
            $this->viewData['amount'] = $this->value;
        }
    }
}
