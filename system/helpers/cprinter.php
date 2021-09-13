<?php
defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
/**
 * @deprecated since 1.2
*/
class cprinter {
    public static function client_protocol() {
        $app = CApp::instance();
        $protocol = ccfg::get('printer_protocol_name');
        if (strlen($protocol) == 0) {
            $protocol = 'cwebrawprint';
        }
        return $protocol;
    }
}
