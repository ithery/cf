<?php

use GuzzleHttp\Psr7\Response;
use Ratchet\ConnectionInterface;
use GuzzleHttp\Psr7\ServerRequest;
use React\Promise\PromiseInterface;
use Ratchet\Http\HttpServerInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

abstract class CWebSocket_Handler_ApiHandlerAbstract implements HttpServerInterface {
    /**
     * The request buffer.
     *
     * @var string
     */
    protected $requestBuffer = '';

    /**
     * The incoming request.
     *
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * The content length that will
     * be calculated.
     *
     * @var int
     */
    protected $contentLength;

    /**
     * The channel manager.
     *
     * @var \CWebSocket_Contract_ChannelManagerInterface
     */
    protected $channelManager;

    /**
     * The app attached with this request.
     *
     * @var null|\CWebSocket_App
     */
    protected $app;

    /**
     * Initialize the request.
     *
     * @return void
     */
    public function __construct() {
        $this->channelManager = CWebSocket::channelManager();
    }

    /**
     * Handle the opened socket connection.
     *
     * @param \Ratchet\ConnectionInterface       $connection
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    public function onOpen(ConnectionInterface $connection, RequestInterface $request = null) {
        $this->request = $request;

        $this->contentLength = $this->findContentLength($request->getHeaders());

        $this->requestBuffer = (string) $request->getBody();

        if (!$this->verifyContentLength()) {
            throw new HttpException(401, 'Invalid content length.');
        }

        $this->handleRequest($connection);
    }

    /**
     * Handle the oncoming message and add it to buffer.
     *
     * @param \Ratchet\ConnectionInterface $from
     * @param mixed                        $msg
     *
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        $this->requestBuffer .= $msg;

        if (!$this->verifyContentLength()) {
            return;
        }

        $this->handleRequest($from);
    }

    /**
     * Handle the socket closing.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    public function onClose(ConnectionInterface $connection) {
    }

    /**
     * Handle the errors.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param Exception                    $exception
     *
     * @return void
     */
    public function onError(ConnectionInterface $connection, Exception $exception) {
        if (!$exception instanceof HttpException) {
            return;
        }

        $response = new Response($exception->getStatusCode(), [
            'Content-Type' => 'application/json',
        ], json_encode([
            'error' => $exception->getMessage(),
        ]));

        c::tap($connection)->send(\GuzzleHttp\Psr7\str($response))->close();
    }

    /**
     * Get the content length from the headers.
     *
     * @param array $headers
     *
     * @return int
     */
    protected function findContentLength(array $headers) {
        $contentLength = CCollection::make($headers)->first(function ($values, $header) {
            return strtolower($header) === 'content-length';
        });

        return isset($contentLength[0]) ? (int) $contentLength[0] : 0;
    }

    /**
     * Check the content length.
     *
     * @return bool
     */
    protected function verifyContentLength() {
        return strlen($this->requestBuffer) === $this->contentLength;
    }

    /**
     * Handle the oncoming connection.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    protected function handleRequest(ConnectionInterface $connection) {
        $serverRequest = (new ServerRequest(
            $this->request->getMethod(),
            $this->request->getUri(),
            $this->request->getHeaders(),
            $this->requestBuffer,
            $this->request->getProtocolVersion()
        ))->withQueryParams(CWebSocket_Server_QueryParameter::create($this->request)->all());

        $httpRequest = CHTTP_Request::createFromBase((new HttpFoundationFactory())->createRequest($serverRequest));

        $this->ensureValidAppId($httpRequest->get('appId'))
            ->ensureValidSignature($httpRequest);
        CHTTP::setRequest($httpRequest);
        // Invoke the controller action
        $response = $this($httpRequest);

        // Allow for async IO in the controller action
        if ($response instanceof PromiseInterface) {
            $response->then(function ($response) use ($connection) {
                $this->sendAndClose($connection, $response);
            });

            return;
        }

        if ($response instanceof HttpException) {
            throw $response;
        }

        $this->sendAndClose($connection, $response);
    }

    /**
     * Send the response and close the connection.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param mixed                        $response
     *
     * @return void
     */
    protected function sendAndClose(ConnectionInterface $connection, $response) {
        c::tap($connection)->send(CHTTP_JsonResponse::create($response))->close();
    }

    /**
     * Ensure app existence.
     *
     * @param mixed $appId
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return $this
     */
    public function ensureValidAppId($appId) {
        if (!$appId || !$this->app = CWebSocket_App::findById($appId)) {
            throw new HttpException(401, "Unknown app id `{$appId}` provided.");
        }

        return $this;
    }

    /**
     * Ensure signature integrity coming from an
     * authorized application.
     *
     * @param \GuzzleHttp\Psr7\ServerRequest $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return $this
     */
    protected function ensureValidSignature(CHTTP_Request $request) {
        // The `auth_signature` & `body_md5` parameters are not included when calculating the `auth_signature` value.
        // The `appId`, `appKey` & `channelName` parameters are actually route parameters and are never supplied by the client.
        if ($request->get('bypass')) {
            return $this;
        }
        $params = carr::except($request->query(), [
            'auth_signature', 'body_md5', 'appId', 'appKey', 'channelName',
        ]);

        if ($request->getContent() !== '') {
            $params['body_md5'] = md5($request->getContent());
        }

        ksort($params);

        $signature = "{$request->getMethod()}\n/{$request->path()}\n" . CWebSocket_Helper::pusherArrayImplode('=', '&', $params);

        $authSignature = hash_hmac('sha256', $signature, $this->app->secret);
        if ($authSignature !== $request->get('auth_signature')) {
            throw new HttpException(401, 'Invalid auth signature provided.');
        }

        return $this;
    }

    /**
     * Handle the incoming request.
     *
     * @return void
     */
    abstract public function __invoke();
}
