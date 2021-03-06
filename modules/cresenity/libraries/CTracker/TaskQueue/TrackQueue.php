<?php

class CTracker_TaskQueue_TrackQueue extends CTracker_TaskQueueAbstract {
    public function execute() {
        $data = carr::get($this->params, 'data');
        $config = carr::get($this->params, 'config');

        //$this->logDaemon('Processing Tracker Queue from ip:' . carr::get($data,'request.clientIp') .' => '.json_encode($data).', config:'.json_encode($config));
        $this->logDaemon('Processing Tracker Queue from ip:' . carr::get($data, 'request.clientIp'));

        try {
            CTracker::populator()->setData($data);
            CTracker::config()->setData($config);
            $tracker = new CTracker_Tracker();
            $tracker->track();
        } catch (CModel_Exception_ModelNotFoundException $ex) {
            $this->logDaemon('Ignore Error: ' . $ex->getMessage() . '');
        }
        $this->logDaemon('Processed Tracker Queue from ip:' . carr::get($data, 'request.clientIp'));
    }
}
