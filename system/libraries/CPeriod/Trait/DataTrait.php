<?php
trait CPeriod_Trait_DataTrait {
    /**
     * @var mixed
     */
    protected $data = null;

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;

        return $this;
    }
}
