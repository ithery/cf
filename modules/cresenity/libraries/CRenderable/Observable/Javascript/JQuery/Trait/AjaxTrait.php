<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 10:38:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CRenderable_Observable_Javascript_JQuery_Trait_AjaxTrait {

    public function ajax($options = array()) {
       
        $this->jQueryStatement()->ajax($options);
        $this->resetJQueryStatement();

        return $this;
    }

}
