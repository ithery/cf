<?php

abstract class CAnalytics_Google_AnalyticGA4_OperatorAbstract {
    public function __construct() {
    }

    /**
     * @return \Google\Protobuf\Internal\Message
     */
    abstract public function toGA4Object();
}
