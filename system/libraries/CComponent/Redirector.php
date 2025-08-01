<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Redirector extends CHTTP_Redirector {
    protected $component;

    public function to($path, $status = 302, $headers = [], $secure = null) {
        $this->component->redirect($this->generator->to($path, [], $secure));

        return $this;
    }

    public function away($path, $status = 302, $headers = []) {
        return $this->to($path, $status, $headers);
    }

    public function component(CComponent $component) {
        $this->component = $component;

        return $this;
    }
}
