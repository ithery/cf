<?php

class CPrinter {
    /**
     * @return CPrinter_EscPos
     */
    public static function escPos() {
        return CPrinter_EscPos::instance();
    }
}
