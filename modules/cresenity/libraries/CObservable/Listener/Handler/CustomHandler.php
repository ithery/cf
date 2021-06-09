<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:27:04 PM
 */
class CObservable_Listener_Handler_CustomHandler extends CObservable_Listener_Handler {
    use CTrait_Compat_Handler_Driver_Custom;

    protected $js;

    public function __construct($listener) {
        parent::__construct($listener);

        $this->name = 'Custom';
    }

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
