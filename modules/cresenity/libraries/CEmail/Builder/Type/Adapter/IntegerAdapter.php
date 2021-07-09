<?php

class CEmail_Builder_Type_Adapter_IntegerAdapter extends CEmail_Builder_Type_AbstractAdapter {
    const MATCHER = '/^integer/im';
    const TYPE = 'integer';

    public function __construct($typeConfig, $value) {
        parent::__construct($typeConfig, $value);
        $this->matchers = ['/\d+/'];
    }
}
