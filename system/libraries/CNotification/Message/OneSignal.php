<?php

class CNotification_Message_Onesignal extends CNotification_MessageAbstract {
    public function send() {
        $oneSignal = CVendor::oneSignal($this->config);
        $tokens = carr::wrap($this->getOption('recipient'));

        $data = $this->getOption('data');
        $androidConfig = $this->getOption('android');
        $apnsConfig = $this->getOption('apns');

        $messaging = $oneSignal->notifications();

        $message = $messaging->add($data);

        // foreach ($multicastReport->successes()->getItems() as $report) {
        //     CDaemon::log('Success send to ' . $report->target()->type() . ':' . $report->target()->value());
        // }
        // foreach ($multicastReport->failures()->getItems() as $report) {
        //     CDaemon::log('Fail send to ' . $report->target()->type() . ':' . $report->target()->value() . ', reason:' . $report->error()->getMessage());
        // }

        // return $multicastReport;
    }
}
