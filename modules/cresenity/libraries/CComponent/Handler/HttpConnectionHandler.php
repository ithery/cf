<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
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
