<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CNotification_TaskQueue_NotificationSender extends CNotification_TaskQueueAbstract {

    protected $params;

    public function __construct($params) {
        $this->params = $params;
    }

    public function execute() {
        $channel = carr::get($this->params, 'channel');
        $options = carr::get($this->params, 'options');
        $className = carr::get($this->params, 'className');
        CNotification::manager()->channel($channel)->sendWithoutQueue($className, $options);
    }

}
