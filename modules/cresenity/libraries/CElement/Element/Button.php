<?php

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 23, 2020, 2:25:21 AM
 */
class CElement_Element_Button extends CElement_Element {
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'button';
    }
}
