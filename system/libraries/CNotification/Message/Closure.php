<?php

defined('SYSPATH') or die('No direct access allowed.');

class CNotification_Message_Closure extends CNotification_MessageAbstract {
    /**
     * @var callable
     */
    protected $closure;

    /**
     * @param callable $closure
     *
     * @return void
     */
    public function setClosure(callable $closure) {
        $this->closure = $closure;
    }

    /**
     * @return mixed
     */
    public function send() {
        return call_user_func_array($this->closure, [$this->options, $this->config]);
    }
}
