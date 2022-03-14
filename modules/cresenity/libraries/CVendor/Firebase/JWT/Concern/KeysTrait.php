<?php

trait CVendor_Firebase_JWT_Concern_KeysTrait {
    /**
     * @var array<string, string>
     */
    private $values = [];

    /**
     * @return array<string, string>
     */
    public function all() {
        return $this->values;
    }
}
