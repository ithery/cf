<?php

class CReport {
    /**
     * @param string $jrxml
     * @param array  $param
     *
     * @return CReport_Jasper
     */
    public static function jasper($jrxml, array $param = []) {
        return new CReport_Jasper($jrxml, $param);
    }

    /**
     * @return CReport_Builder
     */
    public static function builder() {
        return new CReport_Builder();
    }
}
