<?php
defined('SYSPATH') or die('No direct script access.');

class CHTTP_Exception_411 extends CHTTP_Exception_HttpException {
    /**
     * @var int HTTP 411 Length Required
     */
    protected $code = 411;
}
