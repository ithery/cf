<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 1:03:54 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

use OpenTracing\GlobalTracer;

class CDebug {

    protected static $bar;

    /**
     * 
     * @param array $options
     * @return CDebug_Bar
     */
    public static function bar($options = array()) {
        if (self::$bar == null) {
            self::$bar = new CDebug_Bar($options);
        } else {
            self::$bar->setOptions($options);
        }
        return self::$bar;
    }

    public static function dump($var) {
        if (self::bar()->isEnabled()) {

            self::bar()->getCollector('messages')->debug($var);
        }
    }

    public static function tracer() {
        // Creates a tracer with default transport and default propagators
        $tracer = new CDebug_Tracer();

        // Sets a global tracer (singleton). Ideally tracer should be
        // injected as a dependency
        GlobalTracer::set($tracer);

        $application->run();

        // Flushes traces to agent.
        register_shutdown_function(function() {
            GlobalTracer::get()->flush();
        });
    }

}
