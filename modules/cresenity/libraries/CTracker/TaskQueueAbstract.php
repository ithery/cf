<?php

class CTracker_TaskQueueAbstract extends CQueue_AbstractTask {
    protected $params;

    public function __construct($params) {
        $this->params = $params;
    }
}
