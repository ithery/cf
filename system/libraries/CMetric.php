<?php

class CMetric {
    public static function createMetric($name, $value = null) {
        $metric = static::manager()->driver()->create($name, $value);

        return $metric;
    }

    /**
     * @return CMetric_QueryBuilder
     */
    public static function createQuery() {
        $metric = static::manager()->driver()->createQuery();

        return $metric;
    }

    /**
     * @return CMetric_Manager
     */
    public static function manager() {
        return CMetric_Manager::instance();
    }

    public static function flush() {
        return static::manager()->driver()->flush();
    }
}
