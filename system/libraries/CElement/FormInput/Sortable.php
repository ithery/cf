<?php

class CElement_FormInput_Sortable extends CElement_FormInput {
    protected $containerId;

    protected $inputId;

    protected $input;

    protected $container;

    public function __construct($id = null) {
        if ($id == null) {
            $id = spl_object_hash($this);
        }
        $this->id = $id;
        $this->inputId = $id;
        $this->id = $this->id . '-wrapper';
        parent::__construct($this->id);
        $this->containerId = $this->id . '-container';

        $this->tag = 'div';
        $this->isOneTag = false;
        $this->container = CElement_Element_Div::factory($this->containerId);
        $this->input = CElement_FormInput_Hidden::factory($this->inputId);

        $this->add($this->container);
        $this->add($this->input);
    }

    protected function build() {
        parent::build();
        $this->addClass('cres:element:control:Sortable');
        $this->setAttr('cres-element', 'control:Sortable');
        $this->setAttr('cres-config', c::json($this->buildControlConfig()));
    }

    protected function buildControlConfig() {
        $listData = $this->list;
        $keys = $this->value;
        $result = [];
        if ($keys) {
            foreach ($keys as $key) {
                $result[$key] = carr::get($listData, $key, $key);
            }
        } else {
            $result = $listData;
        }
        $config = [
            'list' => $result,
            'containerId' => $this->containerId,
            'inputId' => $this->inputId,
        ];

        return $config;
    }
}
