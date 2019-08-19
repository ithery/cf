<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 27, 2019, 10:54:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_App_Title {

    use CTrait_Element_Property_Title;

    private $showTitle = true;

    
    public function showTitle($bool) {
        $this->showTitle = $bool;
        return $this;
    }

}
