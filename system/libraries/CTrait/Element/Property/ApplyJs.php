<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 9:53:28 PM
 */
trait CTrait_Element_Property_ApplyJs {
    /**
     * @var string
     */
    protected $applyJs;

    /**
     * @param string $applyJs
     *
     * @return $this
     */
    public function setApplyJs($applyJs) {
        $this->applyJs = $applyJs;

        return $this;
    }

    /**
     * @return $this
     */
    public function setApplyJsSelect2() {
        return $this->setApplyJs('select2');
    }

    /**
     * @return string
     */
    public function getApplyJs() {
        return $this->applyJs;
    }
}
