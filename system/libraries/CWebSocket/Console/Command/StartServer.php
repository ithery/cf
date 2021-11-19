<?php

use Illuminate\Support\Facades\Cache;
use React\EventLoop\Factory as LoopFactory;
use BeyondCode\LaravelWebSockets\Facades\StatisticsCollector as StatisticsCollectorFacade;

class CWebSocket_Console_Command_StartServer extends CWebSocket_Console_Command {
    /**
     * The Pusher server instance.
     *
     * @var \Ratchet\Server\IoServer
     */
    public $server;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:serve
        {--host=0.0.0.0}
        {--port=6001}
        {--disable-statistics : Disable the statistics tracking.}
        {--statistics-interval= : The amount of seconds to tick between statistics saving.}
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
     * Get the loop instance.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * Initialize the command.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->loop = LoopFactory::create();
    }

    /**
     * Run the command.
     *
     * @return void
     */
    public function handle() {
        $this->configureLoggers();

        $this->configureManagers();

        $this->configureStatistics();

        $this->configureRestartTimer();

        $this->configureRoutes();

        $this->configurePcntlSignal();

        $this->configurePongTracker();

        $this->startServer();
    }

    /**
     * Configure the loggers used for the console.
     *
     * @return void
     */
    protected function configureLoggers() {
        $this->configureHttpLogger();
        $this->configureMessageLogger();
        $this->configureConnectionLogger();
    }

    /**
     * Register the managers that are not resolved
     * in the package service provider.
     *
     * @return void
     */
    protected function configureManagers() {
        $mode = CF::config('websocket.replication.mode', 'local');
        $class = CF::config('websocket.replication.modes,' . $mode . '.channel_manager');
        $channelManager = new $class(static::loop());

        CWebSocket::setChannelManager($channelManager);
    }

    /**
     * Register the Statistics Collectors that
     * are not resolved in the package service provider.
     *
     * @return void
     */
    protected function configureStatistics() {
        if (!$this->option('disable-statistics')) {
            $intervalInSeconds = $this->option('statistics-interval') ?: CF::config('websocket.statistics.interval_in_seconds', 3600);

            $this->loop->addPeriodicTimer($intervalInSeconds, function () {
                $this->line('Saving statistics...');

                CWebSocket::statisticCollector()->save();
            });
        }
    }

    /**
     * Configure the restart timer.
     *
     * @return void
     */
    public function configureRestartTimer() {
        $this->lastRestart = $this->getLastRestart();

        $this->loop->addPeriodicTimer(10, function () {
            if ($this->getLastRestart() !== $this->lastRestart) {
                $this->triggerSoftShutdown();
            }
        });
    }

    /**
     * Register the routes for the server.
     *
     * @return void
     */
    protected function configureRoutes() {
        CWebSocket::router()->registerRoutes();
    }

    /**
     * Configure the PCNTL signals for soft shutdown.
     *
     * @return void
     */
    protected function configurePcntlSignal() {
        // When the process receives a SIGTERM or a SIGINT
        // signal, it should mark the server as unavailable
        // to receive new connections, close the current connections,
        // then stopping the loop.

        if (!extension_loaded('pcntl')) {
            return;
        }

        $this->loop->addSignal(SIGTERM, function () {
            $this->line('Closing existing connections...');

            $this->triggerSoftShutdown();
        });

        $this->loop->addSignal(SIGINT, function () {
            $this->line('Closing existing connections...');

            $this->triggerSoftShutdown();
        });
    }

    /**
     * Configure the tracker that will delete
     * from the store the connections that.
     *
     * @return void
     */
    protected function configurePongTracker() {
        $this->loop->addPeriodicTimer(10, function () {
            CWebSocket::channelManager()
                ->removeObsoleteConnections();
        });
    }

    /**
     * Configure the HTTP logger class.
     *
     * @return void
     */
    protected function configureHttpLogger() {
        $httpLogger = (new CWebSocket_Server_Logger_HttpLogger($this->output))
            ->enable($this->option('debug') ?: CF::config('app.debug', false))
            ->verbose($this->output->isVerbose());
        CWebSocket::setHttpLogger($httpLogger);
    }

    /**
     * Configure the logger for messages.
     *
     * @return void
     */
    protected function configureMessageLogger() {
        $webSocketLogger = (new CWebSocket_Server_Logger_WebSocketLogger($this->output))
            ->enable($this->option('debug') ?: CF::config('app.debug', false))
            ->verbose($this->output->isVerbose());
        CWebSocket::setWebSocketLogger($webSocketLogger);
    }

    /**
     * Configure the connection logger.
     *
     * @return void
     */
    protected function configureConnectionLogger() {
        $connectionLogger = (new CWebSocket_Server_Logger_ConnectionLogger($this->output))
            ->enable(CF::config('app.debug', false))
            ->verbose($this->output->isVerbose());
        CWebSocket::setConnectionLogger($connectionLogger);
    }

    /**
     * Start the server.
     *
     * @return void
     */
    protected function startServer() {
        $this->info("Starting the WebSocket server on port {$this->option('port')}...");

        $this->buildServer();

        $this->server->run();
    }

    /**
     * Build the server instance.
     *
     * @return void
     */
    protected function buildServer() {
        $this->server = new CWebSocket_ServerFactory(
            $this->option('host'),
            $this->option('port')
        );

        if ($loop = $this->option('loop')) {
            $this->loop = $loop;
        }

        $this->server = $this->server
            ->setLoop($this->loop)
            ->withRoutes(CWebSocket::router()->getRoutes())
            ->setConsoleOutput($this->output)
            ->createServer();
    }

    /**
     * Get the last time the server restarted.
     *
     * @return int
     */
    protected function getLastRestart() {
        return Cache::get(
            'beyondcode:websockets:restart',
            0
        );
    }

    /**
     * Trigger a soft shutdown for the process.
     *
     * @return void
     */
    protected function triggerSoftShutdown() {
        $channelManager = CWebSocket::channelManager();

        // Close the new connections allowance on this server.
        $channelManager->declineNewConnections();

        // Get all local connections and close them. They will
        // be automatically be unsubscribed from all channels.
        $channelManager->getLocalConnections()
            ->then(function ($connections) {
                foreach ($connections as $connection) {
                    $connection->close();
                }
            })
            ->then(function () {
                $this->loop->stop();
            });
    }
}
