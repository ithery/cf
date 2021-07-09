<?php

defined('SYSPATH') or die('No direct access allowed.');

use Dompdf\Dompdf;

class CDOMPDF extends Dompdf {
    public function __construct() {
        parent::__construct();
    }

    public static function factory() {
        return new CDOMPDF();
    }
}
