<?php

trait CNotification_Trait_LogNotificationModelTrait {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'log_notification_id';
        $this->table = 'log_notification';
        $this->guarded = ['log_notification_id'];
    }
}
