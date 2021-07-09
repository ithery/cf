<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
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
