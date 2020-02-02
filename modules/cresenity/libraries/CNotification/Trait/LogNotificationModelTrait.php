<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CNotification_Trait_LogNotificationModelTrait {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->primaryKey = 'log_notification_id';
        $this->table = 'log_notification';
        $this->guarded = array('log_notification_id');
    }

}
