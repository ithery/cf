<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 30, 2020
 */
class CComponent_Redirector extends CHTTP_Redirector {
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
