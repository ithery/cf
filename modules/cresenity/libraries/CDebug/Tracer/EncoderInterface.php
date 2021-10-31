<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 28, 2018, 9:14:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CDebug_Tracer_EncoderInterface {

    /**
     * @param Span[][]|array $traces
     * @return string|StreamInterface
     */
    public function encodeTraces(array $traces);

    /**
     * @return string
     */
    public function getContentType();
}
