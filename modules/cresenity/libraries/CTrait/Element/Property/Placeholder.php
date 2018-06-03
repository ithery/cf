<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 2:38:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_Placeholder {

    /**
     *
     * @var int 
     */
    public $placeholder;

    /**
     * 
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder) {

        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getPlaceholder() {
        return $this->placeholder;
    }

}
