<?php

/**
 * Provides the behavior, features and SQL dialect of the MySQL 8.0 (8.0 GA) database platform.
 */
class CDatabase_Platform_MySql80 extends CDatabase_Platform_MySql57 {
    /**
     * {@inheritdoc}
     */
    protected function getReservedKeywordsClass() {
        return CDatabase_Platform_Keywords_MySql80::class;
    }
}
