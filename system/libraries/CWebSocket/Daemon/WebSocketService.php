<?php

class CWebSocket_Daemon_WebSocketService extends CDaemon_ServiceAbstract {
    protected $loopInterval = 60;

    protected $websocketOptions = [];

    public function setup() {
        $options = [];
        $options['host'] = '0.0.0.0';
        $options['port'] = '6001';
        $options['disableStatistics'] = false;
        $options['statisticInterval'] = CF::config('websocket.statistics.interval_in_seconds', 3600);
        $options['debug'] = CF::config('app.debug');
        $options['loop'] = null;
        $this->websocketOptions = $options;
    }

    public function execute() {
        $process = new CWebSocket_Process_StartServer($this->websocketOptions, new CDaemon_Output());
        $process->start();
    }
}
