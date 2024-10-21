<?php

defined('SYSPATH') or die('No direct access allowed.');

class CNotification_Message_Closure extends CNotification_MessageAbstract {
    protected $closure;

    public function setClosure($closure) {
        $this->closure = $closure;
    }

    public function send() {
        return call_user_func_array($this->closure, [$this->options, $this->config]);
    }
}
