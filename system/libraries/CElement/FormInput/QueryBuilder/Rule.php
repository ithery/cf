<?php
use Illuminate\Contracts\Support\Arrayable;

class CElement_FormInput_QueryBuilder_Rule implements Arrayable {
    /**
     * @var null|string
     */
    public $id;

    /**
     * @var null|string
     */
    public $label;

    /**
     * @var null|string
     */
    public $type;

    /**
     * @var null|array
     */
    public $values;

    /**
     * @param array $ruleData
     *
     * @return void
     */
    public function __construct(array $ruleData) {
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'values' => $this->values
        ];
    }
}
