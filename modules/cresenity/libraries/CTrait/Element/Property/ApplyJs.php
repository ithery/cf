<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 9:53:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_ApplyJs {

    /**
     *
     * @var string
     */
    protected $applyJs;

    /**
     * 
     * @param string $applyJs
     * @return $this
     */
    public function setApplyJs($applyJs) {

        $this->applyJs = $applyJs;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getApplyJs() {
        return $this->applyJs;
    }

}
