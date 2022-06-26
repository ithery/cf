<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *null
 *
 * @since Feb 17, 2018, 12:52:11 AM
 */
trait CTrait_Element_Transform {
    /**
     * @var array
     */
    protected $transforms = [];

    public function addTransform($name, $args = []) {
        $transform = $name;
        if (is_array($args) && !empty($args)) {
            $transform = carr::wrap($transform);
            $transform = array_merge(array_values($args));
        }

        $this->transforms[] = $transform;

        return $this;
    }

    public function applyTransform($value, $data = []) {
        if ($data instanceof CModel) {
            $data = $data->toArray();
        }

        return c::manager()->transform()->call($this->transforms, $value, $data);
    }
}
