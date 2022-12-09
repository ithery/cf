<?php

class CMetric_QueryResult {
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }
}
