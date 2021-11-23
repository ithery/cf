<?php
use Ratchet\Http\Router;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use React\Socket\SecureServer;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Console\Output\OutputInterface;

class CWebSocket_ServerFactory {
    /**
     * The host the server will run on.
     *
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * The port to run on.
     *
     * @var int
     */
    protected $port = 8080;

    /**
     * The event loop instance.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * The routes to register.
     *
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routes;

    /**
     * Console output.
     *
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    protected $consoleOutput;

    /**
     * Initialize the class.
     *
     * @param string $host
     * @param int    $port
     *
     * @return void
     */
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;

        $this->loop = LoopFactory::create();
    }

    /**
     * Add the routes.
     *
     * @param \Symfony\Component\Routing\RouteCollection $routes
     *
     * @return $this
     */
    public function withRoutes(RouteCollection $routes) {
        $this->routes = $routes;

        return $this;
    }

    /**
     * Set the loop instance.
     *
     * @param \React\EventLoop\LoopInterface $loop
     *
     * @return $this
     */
    public function setLoop(LoopInterface $loop) {
        $this->loop = $loop;

        return $this;
    }

    /**
     * Set the console output.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $consoleOutput
     *
     * @return $this
     */
    public function setConsoleOutput(OutputInterface $consoleOutput) {
        $this->consoleOutput = $consoleOutput;

        return $this;
    }

    /**
     * Set up the server.
     *
     * @return \Ratchet\Server\IoServer
     */
    public function createServer() {
        $socket = new Server("{$this->host}:{$this->port}", $this->loop);
        if (CF::config('websocket.ssl.local_cert')) {
            $socket = new SecureServer($socket, $this->loop, CF::config('websocket.ssl'));
        }

        $app = new Router(
            new UrlMatcher($this->routes, new RequestContext())
        );

        $httpServer = new CWebSocket_Server_HttpServer($app, CF::config('websocket.max_request_size_in_kb') * 1024);

        if (CWebSocket_Server_Logger_HttpLogger::isEnabled()) {
            $httpServer = CWebSocket_Server_Logger_HttpLogger::decorate($httpServer);
        }

        return new IoServer($httpServer, $socket, $this->loop);
    }
}
