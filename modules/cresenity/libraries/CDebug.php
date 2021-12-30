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
    protected static $bar;

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
}
