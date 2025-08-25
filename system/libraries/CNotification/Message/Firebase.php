<?php
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class CNotification_Message_Firebase extends CNotification_MessageAbstract {
    public function send() {
        $firebase = CVendor::firebase(carr::get($this->config, 'key'), carr::except($this->config, ['key']));
        $tokens = carr::wrap($this->getOption('recipient'));

        $data = $this->getOption('data');
        $androidConfig = $this->getOption('android');
        $apnsConfig = $this->getOption('apns');

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::new()
            ->withNotification(Notification::create($this->getOption('subject'), $this->getOption('message'), $this->getOption('imageUrl')));

        if (is_array($data)) {
            $message = $message->withData($data);
        }

        if (is_array($androidConfig)) {
            $message = $message->withAndroidConfig($androidConfig);
        }

        if (is_array($apnsConfig)) {
            $message = $message->withApnsConfig($apnsConfig);
        }

        $multicastReport = $messaging->sendMulticast($message, $tokens);

        foreach ($multicastReport->successes()->getItems() as $report) {
            CDaemon::log('Success send to ' . $report->target()->type() . ':' . $report->target()->value());
        }
        foreach ($multicastReport->failures()->getItems() as $report) {
            c::event(new CNotification_Event_FirebaseFailure($report));
            CDaemon::log('Fail send to ' . $report->target()->type() . ':' . $report->target()->value() . ', reason:' . $report->error()->getMessage());
        }

        return $multicastReport;
    }
}
