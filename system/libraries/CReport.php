<?php

class CReport {
    public static function jasper($jrxml, $param) {
        return new CReport_Jasper($jrxml, $param);
    }
}
