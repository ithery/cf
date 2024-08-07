<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CDebug_DebugBar
 */
class CDebug {
    const COLLECTOR_TYPE_DEPRECATED = 'deprecated';

    const COLLECTOR_TYPE_EXCEPTION = 'exception';

    const COLLECTOR_TYPE_PROFILER = 'profiler';

    protected static $bar;

    private static $variables;

    /**
     * @param array $options
     *
     * @return CDebug_DebugBar
     */
    public static function bar($options = []) {
        if (self::$bar == null) {
            self::$bar = new CDebug_DebugBar($options);
        } else {
            self::$bar->setOptions($options);
        }

        return self::$bar;
    }

    public static function dump($var) {
        if (self::bar()->isEnabled()) {
            /** @var CDebug_DataCollector_MessagesCollector $collector */
            $collector = self::bar()->getCollector('messages');
            $collector->debug($var);
        }
    }

    /**
     * @return CDebug_CollectorManager
     */
    public static function collector() {
        return CDebug_CollectorManager::instance();
    }

    public static function variable($key, $value) {
        static::$variables[$key] = $value;
    }

    public static function getVariables() {
        return static::$variables;
    }

    /**
     * @return CDebug_Dumper|CBase_ForwarderStaticClass
     */
    public static function dumper() {
        return new CBase_ForwarderStaticClass(CDebug_Dumper::class);
    }
}
