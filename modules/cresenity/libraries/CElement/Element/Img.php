<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 12:41:06 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Element_Img extends CElement_Element {

    use CTrait_Compat_Element_Img;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->isOneTag = true;
        $this->tag = "img";
    }

    
    public function setSrc($src) {
        $this->setAttr('src',$src);
    }
}
