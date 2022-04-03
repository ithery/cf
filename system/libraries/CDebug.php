<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 1:03:54 PM
 */

use OpenTracing\GlobalTracer;

class CDebug {
    const COLLECTOR_TYPE_DEPRECATED = 'deprecated';

    const COLLECTOR_TYPE_EXCEPTION = 'exception';

    const COLLECTOR_TYPE_PROFILER = 'profiler';

    protected static $bar;

    private static $variables;

    /**
     * @param array $options
     *
     * @return CDebug_Bar
     */
    public static function bar($options = []) {
        if (self::$bar == null) {
            self::$bar = new CDebug_Bar($options);
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
}
