<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CManager_Transform_MethodExecutor
 * @see CManager_Transform_Parser
 * @since Feb 17, 2018, 12:52:11 AM
 */
trait CTrait_Element_Transform {
    /**
     * @var array
     */
    protected $transforms = [];

    public function getTransforms() {
        return $this->transforms;
    }

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

    /**
     * @param mixed $value
     * @param mixed $data
     *
     * @return mixed
     */
    public function applyTransform($value, $data = []) {
        if (empty($this->transforms)) {
            return $value;
        }

        return c::manager()->transform()->call($this->transforms, $value, $data);
    }
}
