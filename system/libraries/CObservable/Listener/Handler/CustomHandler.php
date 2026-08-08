<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Listener_Handler_CustomHandler extends CObservable_Listener_Handler {
    use CTrait_Compat_Handler_Driver_Custom;

    /**
     * @var string
     */
    protected $js;

    /**
     * @param CObservable_Listener $listener
     */
    public function __construct($listener) {
        parent::__construct($listener);

        $this->name = 'Custom';
    }

    /**
     * @param string $js
     *
     * @return $this
     */
    public function setJs($js) {
        $this->js = $js;

        return $this;
    }

    public function js() {
        $js = '';
        $js .= $this->js;

        return $js;
    }
}
