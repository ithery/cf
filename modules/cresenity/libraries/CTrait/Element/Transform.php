<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2018, 12:52:11 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Transform {

    /**
     *
     * @var array
     */
    protected $transform = array();

    public function addTransform($name, $args = array()) {
        $func = CFunction::factory($name);
        if (!is_array($args)) {
            $args = array($args);
        }
        $func->setArgs($args);


        $this->transforms[] = $func;
        return $this;
    }

}
