<?php
use React\EventLoop\Factory as LoopFactory;

use Symfony\Component\Console\Output\OutputInterface;

class CWebSocket_Process_StartServer {
    /**
     * The Pusher server instance.
     *
     * @var \Ratchet\Server\IoServer
     */
    public $server;

    public $disableStatistic = false;

    protected $statisticsInterval = 60;

    protected $debug = false;

    /**
     * Get the loop instance.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    protected $host;

    protected $port;

    protected $output;

    /**
     * Initialize the command.
     *
     * @param mixed      $options
     * @param null|mixed $outputCallback
     *
     * @return void
     */
    public function __construct($options, OutputInterface $output) {
        $this->host = carr::get($options, 'host', '0.0.0.0');
        $this->port = carr::get($options, 'port', '6001');
        $this->disableStatistic = carr::get($options, 'disableStatistic', false);
        $this->statisticInterval = carr::get($options, 'statisticInterval', CF::config('websocket.statistics.interval_in_seconds', 3600));
        $this->debug = carr::get($options, 'debug', CF::config('app.debug', false));
        $this->loop = carr::get($options, 'loop', false);

        $this->output = $output;
        if ($this->loop == null) {
            $this->loop = LoopFactory::create();
        }
    }

    /**
     * Run the command.
     *
     * @return void
     */
    public function start() {
        $this->configureLoggers();

        $this->output->writeln('Logger Configured');
        $this->configureManagers();

        $this->output->writeln('Manager Configured');
        $this->configureStatistics();

        $this->output->writeln('Statistic Configured');
        $this->configureRestartTimer();

        $this->output->writeln('Restart Timer Configured');
        $this->configureRoutes();

        $this->output->writeln('Routes Configured');
        $this->configurePcntlSignal();

        $this->output->writeln('Pcntl Signal Configured');
        $this->configurePongTracker();

        $this->output->writeln('Pong Tracker Configured');
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
        $class = CF::config('websocket.replication.modes.' . $mode . '.channel_manager');
        $channelManager = new $class($this->loop);

        CWebSocket::setChannelManager($channelManager);
    }

    /**
     * Register the Statistics Collectors that
     * are not resolved in the package service provider.
     *
     * @return void
     */
    protected function configureStatistics() {
        if (!$this->disableStatistic) {
            $intervalInSeconds = $this->statisticsInterval ?: CF::config('websocket.statistics.interval_in_seconds', 3600);

            $this->loop->addPeriodicTimer($intervalInSeconds, function () {
                $this->writeLn('Saving statistics...');

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
            $this->writeLn('Closing existing connections...');

            $this->triggerSoftShutdown();
        });

        $this->loop->addSignal(SIGINT, function () {
            $this->writeLn('Closing existing connections...');

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
            ->enable($this->debug)
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
            ->enable($this->debug)
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
        $this->writeLn("Starting the WebSocket server {$this->host} on port {$this->port}...");

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
            $this->host,
            $this->port
        );

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
        return 0;
        // return Cache::get(
        //     'cf:websockets:restart',
        //     0
        // );
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

    public function writeLn($line) {
        if ($this->output) {
            $this->output->writeln($line);
        }
    }
}
