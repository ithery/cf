<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CComponent_Handler_ConnectionHandler extends CComponent_HandlerAbstract {
    public function handle($payload) {
        return CComponent_LifecycleManager::fromSubsequentRequest($payload)
            ->hydrate()
            ->renderToView()
            ->dehydrate()
            ->toSubsequentResponse();
    }
}
