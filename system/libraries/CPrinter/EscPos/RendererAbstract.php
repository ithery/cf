<?php

abstract class CPrinter_EscPos_RendererAbstract {
    protected $data;

    /**
     * @var null|CPrinter_EscPos_CapabilityProfile
     */
    protected $profile;

    public function __construct($data, CPrinter_EscPos_CapabilityProfile $profile = null) {
        $this->data = $data;
        $this->profile = $profile;
    }
}
