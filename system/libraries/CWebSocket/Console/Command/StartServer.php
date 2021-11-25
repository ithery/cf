<?php

use React\EventLoop\Factory as LoopFactory;

class CWebSocket_Console_Command_StartServer extends CWebSocket_Console_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:serve
        {--host=0.0.0.0}
        {--port=6001}
        {--disable-statistic : Disable the statistics tracking.}
        {--statistic-interval= : The amount of seconds to tick between statistics saving.}
        {--debug : Forces the loggers to be enabled and thereby overriding the APP_DEBUG setting.}
        {--loop : Programatically inject the loop.}
    ';

    /**
     * The console command description.
     *
     * @var null|string
     */
    protected $description = 'Start the CF WebSocket server.';

    /**
     * Initialize the command.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Run the command.
     *
     * @return void
     */
    public function handle() {
        $options = [];
        $options['host'] = $this->option('host');
        $options['port'] = $this->option('port');
        $options['disableStatistic'] = $this->option('disable-statistic');
        $options['statisticInterval'] = $this->option('statistic-interval') ?: CF::config('websocket.statistics.interval_in_seconds', 3600);
        $options['debug'] = $this->option('debug');
        $options['loop'] = $this->option('loop');
        $this->info('DOCROOT:' . DOCROOT);
        $process = new CWebSocket_Process_StartServer($options, $this->output);
        $process->start();
    }
}
