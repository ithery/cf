<?php

class CTesting {
    public static function isInstalled() {
        $phpUnitBinaryExists = file_exists(static::phpUnitBinary());
        $phpUnitConfigExists = file_exists(static::phpUnitConfig());
        $testDirectoryExists = CFile::isDirectory(static::testDirectory());
        $testUnitDirectoryExists = CFile::isDirectory(static::testUnitDirectory());
        $testFeatureDirectoryExists = CFile::isDirectory(static::testFeatureDirectory());
        return $phpUnitBinaryExists
            && $phpUnitConfigExists
            && $testDirectoryExists
            && $testUnitDirectoryExists
            && $testFeatureDirectoryExists;
    }

    public static function phpUnitBinary() {
        return DOCROOT . '.bin' . DS . 'phpunit' . DS . 'phpunit';
    }

    public static function phpUnitConfig() {
        return c::fixPath(CF::appDir()) . 'phpunit.xml';
    }

    public static function testDirectory() {
        return c::fixPath(CF::appDir()) . 'default' . DS . 'tests';
    }

    public static function testUnitDirectory() {
        return static::testDirectory() . DS . 'Unit';
    }

    public static function testFeatureDirectory() {
        return static::testDirectory() . DS . 'Feature';
    }
}
