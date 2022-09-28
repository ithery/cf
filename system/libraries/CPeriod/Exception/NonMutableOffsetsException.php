<?php

class CPeriod_Exception_NonMutableOffsetsException extends CPeriod_Exception {
    public static function forClass($className) {
        return new self("Offsets of `{$className}` objects are not mutable.");
    }
}
