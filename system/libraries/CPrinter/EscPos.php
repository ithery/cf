<?php

/**
 * @see CPrinter
 */
class CPrinter_EscPos {
    /**
     * @var CPrinter_EscPos
     */
    private static $instance;

    /**
     * @return CPrinter_EscPos
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CPrinter_EscPos();
        }

        return self::$instance;
    }

    public function getProfileNames() {
        return CPrinter_EscPos_CapabilityProfile::getProfileNames();
    }
    public function createBuilder() {
        return new CPrinter_EscPos_Builder();
    }
}
