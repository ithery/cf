<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Nov 12, 2017, 3:34:27 AM
 */
class CElement_Element_H5 extends CElement_Element {
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'h5';
    }
}
