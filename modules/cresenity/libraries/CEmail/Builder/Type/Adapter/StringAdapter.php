<?php

class CEmail_Builder_Type_Adapter_StringAdapter extends CEmail_Builder_Type_AbstractAdapter {
    const MATCHER = '/^string/im';
    const TYPE = 'string';

    public function __construct($typeConfig, $value) {
        parent::__construct($typeConfig, $value);
        $this->matchers = ['/.*/'];
    }
}
