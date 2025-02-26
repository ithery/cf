<?php

abstract class CReport_Hook_HookAbstract implements CReport_Hook_HookInterface {
    protected $generator;

    public function __construct(CReport_Generator $generator) {
        $this->generator = $generator;
    }
}
