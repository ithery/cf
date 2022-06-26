<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 12:52:11 AM
 */
trait CTrait_Element_Transform {
    /**
     * @var array
     */
    protected $transforms = [];

    public function addTransform($name, $args = []) {
        $func = CFunction::factory($name);
        if (!is_array($args)) {
            $args = [$args];
        }
        $func->setArgs($args);

        $this->transforms[] = $func;

        return $this;
    }
}
