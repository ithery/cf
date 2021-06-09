<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Oct 30, 2020
 */
class CApp_Element extends CObservable {
    public function __construct($id = '') {
        parent::__construct($id);
    }
}
