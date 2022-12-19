<?php

abstract class CImage_Chart_EngineAbstract {
    protected $builder;

    public function __construct(CImage_Chart_Builder $builder) {
        $this->builder = $builder;
    }

    abstract public function toUri();
}
