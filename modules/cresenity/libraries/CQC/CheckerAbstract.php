<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Oct 7, 2020 
 * @license Ittron Global Teknologi
 */


abstract class CQC_CheckerAbstract {
    
    
    
    public function getName() {
        $className = get_called_class();
        return carr::last(explode("_",$className));
    }
}