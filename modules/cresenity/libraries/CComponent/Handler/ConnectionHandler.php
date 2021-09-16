<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
abstract class CComponent_Handler_ConnectionHandler extends CComponent_HandlerAbstract {
    public function handle($payload) {
        return CComponent_LifecycleManager::fromSubsequentRequest($payload)
            ->hydrate()
            ->renderToView()
            ->dehydrate()
            ->toSubsequentResponse();
    }
}
