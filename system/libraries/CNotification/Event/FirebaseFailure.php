<?php

class CNotification_Event_FirebaseFailure {
    /**
     * @var CVendor_Firebase_Messaging_SendReport
     */
    public $report;

    public function __construct(CVendor_Firebase_Messaging_SendReport $report) {
        $this->report = $report;
    }
}
