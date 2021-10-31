<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 28, 2018, 9:21:29 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Propagator implementations should be able to inject and extract
 * SpanContexts into an implementation specific carrier.
 */
interface CDebug_Tracer_PropagatorInterface {

    /**
     * Inject takes the SpanContext and injects it into the carrier using
     * an implementation specific method.
     *
     * @param SpanContext $spanContext
     * @param array|\ArrayAccess $carrier
     * @return void
     */
    public function inject(SpanContext $spanContext, &$carrier);

    /**
     * Extract returns the SpanContext from the given carrier using an
     * implementation specific method.
     *
     * @param array|\ArrayAccess $carrier
     * @return SpanContext
     */
    public function extract($carrier);
}
