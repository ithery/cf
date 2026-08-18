<?php

defined('SYSPATH') or die('No direct access allowed.');

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
