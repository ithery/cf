<?php

class CComparator {
    public static function createFactory() {
        return new CComparator_Factory();
    }

    public static function createExporter() {
        return new CComparator_Exporter();
    }
}
