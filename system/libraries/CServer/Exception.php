<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Induk seluruh galat CServer.
 *
 * Adanya induk tersendiri membuat pemanggil dapat menangkap segala kegagalan
 * pengelolaan server dengan satu catch, tanpa ikut menelan galat lain yang
 * kebetulan lewat.
 *
 * Diturunkan dari CException agar penulisan pesan dapat memakai penanda :var
 * seperti galat kerangka kerja lainnya.
 */
class CServer_Exception extends CException {
}
