<?php
use Kreait\Firebase\Messaging\SendReport;

class CNotification_Event_FirebaseFailure {
    /**
     * @var SendReport
     */
    public $report;

    public function __construct(SendReport $report) {
        $this->report = $report;
    }
}
