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

    public function addTransform($transform) {
        $transform = carr::wrap($transform);

        //serialize when closure
        foreach ($transform as $key => $t) {
            if ($t instanceof Closure) {
                $transform[$key] = new \Opis\Closure\SerializableClosure($t);
            }
        }
        $this->transforms = array_merge(
            $this->transforms,
            array_values($transform)
        );

        return $this;
    }

    public function applyTransform($value, $data = []) {
        return c::manager()->transform()->call($this->transforms, $value, $data);
    }
}
