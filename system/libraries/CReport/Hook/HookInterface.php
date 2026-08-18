<?php

interface CReport_Hook_HookInterface {
    public function __construct(CReport_Generator $generator);

    public function getValue();
}
