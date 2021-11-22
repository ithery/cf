<?php

class CExporter_Event_ImportFailed {
    /**
     * @var Throwable
     */
    public $e;

    /**
     * @param Throwable $e
     */
    public function __construct(Throwable $e) {
        $this->e = $e;
    }

    /**
     * @return Throwable
     */
    public function getException() {
        return $this->e;
    }
}
