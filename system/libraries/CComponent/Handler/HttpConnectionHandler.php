<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_Handler_HttpConnectionHandler extends CComponent_Handler_ConnectionHandler {
    public function __invoke() {
        return $this->handle(
            c::request([
                'fingerprint',
                'serverMemo',
                'updates',
            ])
        );
    }
}
