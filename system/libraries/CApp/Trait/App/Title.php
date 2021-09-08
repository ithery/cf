<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 27, 2019, 10:54:01 PM
 */
trait CApp_Trait_App_Title {
    use CTrait_Element_Property_Title;

    private $showTitle = true;

    public function showTitle($bool) {
        $this->showTitle = $bool;
        return $this;
    }
}
