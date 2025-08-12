<?php
use Illuminate\Contracts\Support\Arrayable;

class CElement_FormInput_QueryBuilder_Rule implements Arrayable {
    public $id;

    public $label;

    public $type;

    public $values;

    public function __construct(array $ruleData) {
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'values' => $this->values
        ];
    }
}
