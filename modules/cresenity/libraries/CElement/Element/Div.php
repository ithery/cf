<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 28, 2017, 2:25:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Element_Div extends CElement_Element {

    use CTrait_Element_Handler_ReloadHandler;
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }
    
    
    protected function build() {
        parent::build();
        $this->bootBuildReloadHandler();
    }

    

}
