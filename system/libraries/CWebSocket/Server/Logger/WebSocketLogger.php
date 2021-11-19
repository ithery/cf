<?php

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class CWebSocket_Server_Logger_WebSocketLogger extends CWebSocket_Server_Logger implements MessageComponentInterface {
    /**
     * The HTTP app instance to watch.
     *
     * @var \Ratchet\Http\HttpServerInterface
     */
    protected $app;

    /**
     * Create a new instance and add the app to watch.
     *
     * @param \Ratchet\MessageComponentInterface $app
     *
     * @return self
     */
    public static function decorate(MessageComponentInterface $app) {
        $logger = clone CWebSocket::webSocketLogger();

        return $logger->setApp($app);
    }

    /**
     * Set a new app to watch.
     *
     * @param \Ratchet\MessageComponentInterface $app
     *
     * @return $this
     */
    public function setApp(MessageComponentInterface $app) {
        $this->app = $app;

        return $this;
    }

    /**
     * Handle the HTTP open request.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    public function onOpen(ConnectionInterface $connection) {
        $appKey = CWebSocket_Server_QueryParameter::create($connection->httpRequest)->get('appKey');

        $this->warn("New connection opened for app key {$appKey}.");

        $this->app->onOpen(CWebSocket_Server_Logger_ConnectionLogger::decorate($connection));
    }

    /**
     * Handle the HTTP message request.
     *
     * @param \Ratchet\ConnectionInterface                $connection
     * @param \Ratchet\RFC6455\Messaging\MessageInterface $message
     *
     * @return void
     */
    public function onMessage(ConnectionInterface $connection, MessageInterface $message) {
        $this->info("{$connection->app->id}: connection id {$connection->socketId} received message: {$message->getPayload()}.");

        $this->app->onMessage(CWebSocket_Server_Logger_ConnectionLogger::decorate($connection), $message);
    }

    /**
     * Handle the HTTP close request.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    public function onClose(ConnectionInterface $connection) {
        $socketId = $connection->socketId ?? null;

        $this->warn("Connection id {$socketId} closed.");

        $this->app->onClose(CWebSocket_Server_Logger_ConnectionLogger::decorate($connection));
    }

    /**
     * Handle HTTP errors.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param Exception                    $exception
     *
     * @return void
     */
    public function onError(ConnectionInterface $connection, Exception $exception) {
        $exceptionClass = get_class($exception);

        $appId = $connection->app->id ?? 'Unknown app id';

        $message = "{$appId}: exception `{$exceptionClass}` thrown: `{$exception->getMessage()}`.";

        if ($this->verbose) {
            $message .= $exception->getTraceAsString();
        }

        $this->error($message);

        $this->app->onError(CWebSocket_Server_Logger_ConnectionLogger::decorate($connection), $exception);
    }
}
