<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 28, 2018, 9:09:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CDebug_Tracer_TransportInterface {

    /**
     * @param OpenTracing\Span[][] $traces
     */
    public function send(array $traces);

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setHeader($key, $value);
}
