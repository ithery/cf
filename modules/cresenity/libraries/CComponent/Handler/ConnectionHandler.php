<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
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
