<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Cookie\CookieJar;
use Psr\Http\Message\MessageInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\VarDumper\VarDumper;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Contracts\Support\Arrayable;

class CHTTP_Client_PendingRequest {
    use CTrait_Conditionable, CTrait_Macroable;

    /**
     * The factory instance.
     *
     * @var null|\CHTTP_Client
     */
    protected $factory;

    /**
     * The Guzzle client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The base URL for the request.
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * The request body format.
     *
     * @var string
     */
    protected $bodyFormat;

    /**
     * The raw body for the request.
     *
     * @var null|string
     */
    protected $pendingBody;

    /**
     * The pending files for the request.
     *
     * @var array
     */
    protected $pendingFiles = [];

    /**
     * The request cookies.
     *
     * @var array
     */
    protected $cookies;

    /**
     * The transfer stats for the request.
     *
     * \GuzzleHttp\TransferStats
     */
    protected $transferStats;

    /**
     * The request options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The number of times to try the request.
     *
     * @var int
     */
    protected $tries = 1;

    /**
     * The number of milliseconds to wait between retries.
     *
     * @var int
     */
    protected $retryDelay = 100;

    /**
     * Whether to throw an exception when all retries fail.
     *
     * @var bool
     */
    protected $retryThrow = true;

    /**
     * The callback that will determine if the request should be retried.
     *
     * @var null|callable
     */
    protected $retryWhenCallback = null;

    /**
     * The callbacks that should execute before the request is sent.
     *
     * @var \CCollection
     */
    protected $beforeSendingCallbacks;

    /**
     * The stub callables that will handle requests.
     *
     * @var null|\CCollection
     */
    protected $stubCallbacks;

    /**
     * The middleware callables added by users that will handle requests.
     *
     * @var \CCollection
     */
    protected $middleware;

    /**
     * Whether the requests should be asynchronous.
     *
     * @var bool
     */
    protected $async = false;

    /**
     * The pending request promise.
     *
     * @var \GuzzleHttp\Promise\PromiseInterface
     */
    protected $promise;

    /**
     * The sent request object, if a request has been made.
     *
     * @var null|\CHTTP_Client_Request
     */
    protected $request;

    /**
     * The Guzzle request options that are mergable via array_merge_recursive.
     *
     * @var array
     */
    protected $mergableOptions = [
        'cookies',
        'form_params',
        'headers',
        'json',
        'multipart',
        'query',
    ];

    /**
     * Create a new HTTP Client instance.
     *
     * @param null|\CHTTP_Client $factory
     *
     * @return void
     */
    public function __construct(CHTTP_Client $factory = null) {
        $this->factory = $factory;
        $this->middleware = new CCollection();

        $this->asJson();

        $this->options = [
            'connect_timeout' => 10,
            'http_errors' => false,
            'timeout' => 30,
        ];

        $this->beforeSendingCallbacks = c::collect([function (CHTTP_Client_Request $request, array $options, CHTTP_Client_PendingRequest $pendingRequest) {
            $pendingRequest->request = $request;
            $pendingRequest->cookies = $options['cookies'];

            $pendingRequest->dispatchRequestSendingEvent();
        }]);
    }

    /**
     * Set the base URL for the pending request.
     *
     * @param string $url
     *
     * @return $this
     */
    public function baseUrl(string $url) {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Attach a raw body to the request.
     *
     * @param string $content
     * @param string $contentType
     *
     * @return $this
     */
    public function withBody($content, $contentType) {
        $this->bodyFormat('body');

        $this->pendingBody = $content;

        $this->contentType($contentType);

        return $this;
    }

    /**
     * Indicate the request contains JSON.
     *
     * @return $this
     */
    public function asJson() {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    /**
     * Indicate the request contains form parameters.
     *
     * @return $this
     */
    public function asForm() {
        return $this->bodyFormat('form_params')->contentType('application/x-www-form-urlencoded');
    }

    /**
     * Attach a file to the request.
     *
     * @param string|array    $name
     * @param string|resource $contents
     * @param null|string     $filename
     * @param array           $headers
     *
     * @return $this
     */
    public function attach($name, $contents = '', $filename = null, array $headers = []) {
        if (is_array($name)) {
            foreach ($name as $file) {
                $this->attach(...$file);
            }

            return $this;
        }

        $this->asMultipart();

        $this->pendingFiles[] = array_filter([
            'name' => $name,
            'contents' => $contents,
            'headers' => $headers,
            'filename' => $filename,
        ]);

        return $this;
    }

    /**
     * Indicate the request is a multi-part form request.
     *
     * @return $this
     */
    public function asMultipart() {
        return $this->bodyFormat('multipart');
    }

    /**
     * Specify the body format of the request.
     *
     * @param string $format
     *
     * @return $this
     */
    public function bodyFormat(string $format) {
        return c::tap($this, function ($request) use ($format) {
            $this->bodyFormat = $format;
        });
    }

    /**
     * Specify the request's content type.
     *
     * @param string $contentType
     *
     * @return $this
     */
    public function contentType(string $contentType) {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    /**
     * Indicate that JSON should be returned by the server.
     *
     * @return $this
     */
    public function acceptJson() {
        return $this->accept('application/json');
    }

    /**
     * Indicate the type of content that should be returned by the server.
     *
     * @param string $contentType
     *
     * @return $this
     */
    public function accept($contentType) {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    /**
     * Add the given headers to the request.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function withHeaders(array $headers) {
        return c::tap($this, function ($request) use ($headers) {
            return $this->options = array_merge_recursive($this->options, [
                'headers' => $headers,
            ]);
        });
    }

    /**
     * Specify the basic authentication username and password for the request.
     *
     * @param string $username
     * @param string $password
     *
     * @return $this
     */
    public function withBasicAuth(string $username, string $password) {
        return c::tap($this, function ($request) use ($username, $password) {
            return $this->options['auth'] = [$username, $password];
        });
    }

    /**
     * Specify the digest authentication username and password for the request.
     *
     * @param string $username
     * @param string $password
     *
     * @return $this
     */
    public function withDigestAuth($username, $password) {
        return c::tap($this, function ($request) use ($username, $password) {
            return $this->options['auth'] = [$username, $password, 'digest'];
        });
    }

    /**
     * Specify an authorization token for the request.
     *
     * @param string $token
     * @param string $type
     *
     * @return $this
     */
    public function withToken($token, $type = 'Bearer') {
        return c::tap($this, function ($request) use ($token, $type) {
            return $this->options['headers']['Authorization'] = trim($type . ' ' . $token);
        });
    }

    /**
     * Specify the user agent for the request.
     *
     * @param string $userAgent
     *
     * @return $this
     */
    public function withUserAgent($userAgent) {
        return c::tap($this, function ($request) use ($userAgent) {
            return $this->options['headers']['User-Agent'] = trim($userAgent);
        });
    }

    /**
     * Specify the cookies that should be included with the request.
     *
     * @param array  $cookies
     * @param string $domain
     *
     * @return $this
     */
    public function withCookies(array $cookies, string $domain) {
        return c::tap($this, function ($request) use ($cookies, $domain) {
            return $this->options = array_merge_recursive($this->options, [
                'cookies' => CookieJar::fromArray($cookies, $domain),
            ]);
        });
    }

    /**
     * Indicate that redirects should not be followed.
     *
     * @return $this
     */
    public function withoutRedirecting() {
        return c::tap($this, function ($request) {
            return $this->options['allow_redirects'] = false;
        });
    }

    /**
     * Indicate that TLS certificates should not be verified.
     *
     * @return $this
     */
    public function withoutVerifying() {
        return c::tap($this, function ($request) {
            return $this->options['verify'] = false;
        });
    }

    /**
     * Specify the path where the body of the response should be stored.
     *
     * @param string|resource $to
     *
     * @return $this
     */
    public function sink($to) {
        return c::tap($this, function ($request) use ($to) {
            return $this->options['sink'] = $to;
        });
    }

    /**
     * Specify the timeout (in seconds) for the request.
     *
     * @param int $seconds
     *
     * @return $this
     */
    public function timeout(int $seconds) {
        return c::tap($this, function () use ($seconds) {
            $this->options['timeout'] = $seconds;
        });
    }

    /**
     * Specify the connect timeout (in seconds) for the request.
     *
     * @param int $seconds
     *
     * @return $this
     */
    public function connectTimeout(int $seconds) {
        return c::tap($this, function () use ($seconds) {
            $this->options['connect_timeout'] = $seconds;
        });
    }

    /**
     * Specify the number of times the request should be attempted.
     *
     * @param int           $times
     * @param int           $sleep
     * @param null|callable $when
     * @param bool          $throw
     *
     * @return $this
     */
    public function retry(int $times, int $sleep = 0, ?callable $when = null, bool $throw = true) {
        $this->tries = $times;
        $this->retryDelay = $sleep;
        $this->retryThrow = $throw;
        $this->retryWhenCallback = $when;

        return $this;
    }

    /**
     * Replace the specified options on the request.
     *
     * @param array $options
     *
     * @return $this
     */
    public function withOptions(array $options) {
        return c::tap($this, function ($request) use ($options) {
            return $this->options = array_replace_recursive(
                array_merge_recursive($this->options, carr::only($options, $this->mergableOptions)),
                $options
            );
        });
    }

    /**
     * Add new middleware the client handler stack.
     *
     * @param callable $middleware
     *
     * @return $this
     */
    public function withMiddleware(callable $middleware) {
        $this->middleware->push($middleware);

        return $this;
    }

    /**
     * Add a new "before sending" callback to the request.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function beforeSending($callback) {
        return c::tap($this, function () use ($callback) {
            $this->beforeSendingCallbacks[] = $callback;
        });
    }

    /**
     * Dump the request before sending.
     *
     * @return $this
     */
    public function dump() {
        $values = func_get_args();

        return $this->beforeSending(function (CHTTP_Client_Request $request, array $options) use ($values) {
            foreach (array_merge($values, [$request, $options]) as $value) {
                VarDumper::dump($value);
            }
        });
    }

    /**
     * Dump the request before sending and end the script.
     *
     * @return $this
     */
    public function dd() {
        $values = func_get_args();

        return $this->beforeSending(function (CHTTP_Client_Request $request, array $options) use ($values) {
            foreach (array_merge($values, [$request, $options]) as $value) {
                VarDumper::dump($value);
            }

            exit(1);
        });
    }

    /**
     * Issue a GET request to the given URL.
     *
     * @param string            $url
     * @param null|array|string $query
     *
     * @return \CHTTP_Client_Response
     */
    public function get(string $url, $query = null) {
        return $this->send('GET', $url, [
            'query' => $query,
        ]);
    }

    /**
     * Issue a HEAD request to the given URL.
     *
     * @param string            $url
     * @param null|array|string $query
     *
     * @return \CHTTP_Client_Response
     */
    public function head(string $url, $query = null) {
        return $this->send('HEAD', $url, [
            'query' => $query,
        ]);
    }

    /**
     * Issue a POST request to the given URL.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \CHTTP_Client_Response
     */
    public function post(string $url, $data = []) {
        return $this->send('POST', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a PATCH request to the given URL.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \CHTTP_Client_Response
     */
    public function patch($url, $data = []) {
        return $this->send('PATCH', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a PUT request to the given URL.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \CHTTP_Client_Response
     */
    public function put($url, $data = []) {
        return $this->send('PUT', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a DELETE request to the given URL.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \CHTTP_Client_Response
     */
    public function delete($url, $data = []) {
        return $this->send('DELETE', $url, empty($data) ? [] : [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Send a pool of asynchronous requests concurrently.
     *
     * @param callable $callback
     *
     * @return array
     */
    public function pool(callable $callback) {
        $results = [];

        $requests = c::tap(new CHTTP_Client_Pool($this->factory), $callback)->getRequests();

        foreach ($requests as $key => $item) {
            $results[$key] = $item instanceof static ? $item->getPromise()->wait() : $item->wait();
        }

        return $results;
    }

    /**
     * Send the request to the given URL.
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @throws \Exception
     *
     * @return \CHTTP_Client_Response|\GuzzleHttp\Promise\PromiseInterface
     */
    public function send(string $method, string $url, array $options = []) {
        $url = ltrim(rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'), '/');

        $options = $this->parseHttpOptions($options);

        list($this->pendingBody, $this->pendingFiles) = [null, []];

        if ($this->async) {
            return $this->makePromise($method, $url, $options);
        }

        return c::retry($this->tries ?: 1, function () use ($method, $url, $options) {
            try {
                return c::tap(new CHTTP_Client_Response($this->sendRequest($method, $url, $options)), function ($response) {
                    $this->populateResponse($response);

                    if ($this->tries > 1 && $this->retryThrow && !$response->successful()) {
                        $response->throw();
                    }

                    $this->dispatchResponseReceivedEvent($response);
                });
            } catch (ConnectException $e) {
                $this->dispatchConnectionFailedEvent();

                throw new CHTTP_Client_Exception_ConnectionException($e->getMessage(), 0, $e);
            }
        }, $this->retryDelay ?? 100, $this->retryWhenCallback);
    }

    /**
     * Parse the given HTTP options and set the appropriate additional options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseHttpOptions(array $options) {
        if (isset($options[$this->bodyFormat])) {
            if ($this->bodyFormat === 'multipart') {
                $options[$this->bodyFormat] = $this->parseMultipartBodyFormat($options[$this->bodyFormat]);
            } elseif ($this->bodyFormat === 'body') {
                $options[$this->bodyFormat] = $this->pendingBody;
            }

            if (is_array($options[$this->bodyFormat])) {
                $options[$this->bodyFormat] = array_merge(
                    $options[$this->bodyFormat],
                    $this->pendingFiles
                );
            }
        } else {
            $options[$this->bodyFormat] = $this->pendingBody;
        }

        return c::collect($options)->map(function ($value, $key) {
            if ($key === 'json' && $value instanceof JsonSerializable) {
                return $value;
            }

            return $value instanceof Arrayable ? $value->toArray() : $value;
        })->all();
    }

    /**
     * Parse multi-part form data.
     *
     * @param array $data
     *
     * @return array|array[]
     */
    protected function parseMultipartBodyFormat(array $data) {
        return c::collect($data)->map(function ($value, $key) {
            return is_array($value) ? $value : ['name' => $key, 'contents' => $value];
        })->values()->all();
    }

    /**
     * Send an asynchronous request to the given URL.
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function makePromise(string $method, string $url, array $options = []) {
        return $this->promise = $this->sendRequest($method, $url, $options)
            ->then(function (MessageInterface $message) {
                return c::tap(new CHTTP_Client_Response($message), function ($response) {
                    $this->populateResponse($response);
                    $this->dispatchResponseReceivedEvent($response);
                });
            })
            ->otherwise(function (TransferException $e) {
                return $e instanceof RequestException ? $this->populateResponse(new CHTTP_Client_Response($e->getResponse())) : $e;
            });
    }

    /**
     * Send a request either synchronously or asynchronously.
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\MessageInterface|\GuzzleHttp\Promise\PromiseInterface
     */
    protected function sendRequest(string $method, string $url, array $options = []) {
        $clientMethod = $this->async ? 'requestAsync' : 'request';

        $laravelData = $this->parseRequestData($method, $url, $options);

        return $this->buildClient()->$clientMethod($method, $url, $this->mergeOptions([
            'laravel_data' => $laravelData,
            'on_stats' => function ($transferStats) {
                $this->transferStats = $transferStats;
            },
        ], $options));
    }

    /**
     * Get the request data as an array so that we can attach it to the request for convenient assertions.
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return array
     */
    protected function parseRequestData($method, $url, array $options) {
        $laravelData = $options[$this->bodyFormat] ?? $options['query'] ?? [];

        $urlString = cstr::of($url);

        if (empty($laravelData) && $method === 'GET' && $urlString->contains('?')) {
            $laravelData = (string) $urlString->after('?');
        }

        if (is_string($laravelData)) {
            parse_str($laravelData, $parsedData);

            $laravelData = is_array($parsedData) ? $parsedData : [];
        }

        if ($laravelData instanceof JsonSerializable) {
            $laravelData = $laravelData->jsonSerialize();
        }

        return is_array($laravelData) ? $laravelData : [];
    }

    /**
     * Populate the given response with additional data.
     *
     * @param \CHTTP_Client_Response $response
     *
     * @return \CHTTP_Client_Response
     */
    protected function populateResponse(CHTTP_Client_Response $response) {
        $response->cookies = $this->cookies;

        $response->transferStats = $this->transferStats;

        return $response;
    }

    /**
     * Build the Guzzle client.
     *
     * @return \GuzzleHttp\Client
     */
    public function buildClient() {
        return $this->requestsReusableClient()
               ? $this->getReusableClient()
               : $this->createClient($this->buildHandlerStack());
    }

    /**
     * Determine if a reusable client is required.
     *
     * @return bool
     */
    protected function requestsReusableClient() {
        return !is_null($this->client) || $this->async;
    }

    /**
     * Retrieve a reusable Guzzle client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getReusableClient() {
        return $this->client = $this->client ?: $this->createClient($this->buildHandlerStack());
    }

    /**
     * Create new Guzzle client.
     *
     * @param \GuzzleHttp\HandlerStack $handlerStack
     *
     * @return \GuzzleHttp\Client
     */
    public function createClient($handlerStack) {
        return new Client([
            'handler' => $handlerStack,
            'cookies' => true,
        ]);
    }

    /**
     * Build the Guzzle client handler stack.
     *
     * @return \GuzzleHttp\HandlerStack
     */
    public function buildHandlerStack() {
        return $this->pushHandlers(HandlerStack::create());
    }

    /**
     * Add the necessary handlers to the given handler stack.
     *
     * @param \GuzzleHttp\HandlerStack $handlerStack
     *
     * @return \GuzzleHttp\HandlerStack
     */
    public function pushHandlers($handlerStack) {
        return c::tap($handlerStack, function ($stack) {
            $stack->push($this->buildBeforeSendingHandler());
            $stack->push($this->buildRecorderHandler());

            $this->middleware->each(function ($middleware) use ($stack) {
                $stack->push($middleware);
            });

            $stack->push($this->buildStubHandler());
        });
    }

    /**
     * Build the before sending handler.
     *
     * @return \Closure
     */
    public function buildBeforeSendingHandler() {
        return function ($handler) {
            return function ($request, $options) use ($handler) {
                return $handler($this->runBeforeSendingCallbacks($request, $options), $options);
            };
        };
    }

    /**
     * Build the recorder handler.
     *
     * @return \Closure
     */
    public function buildRecorderHandler() {
        return function ($handler) {
            return function ($request, $options) use ($handler) {
                $promise = $handler($request, $options);

                return $promise->then(function ($response) use ($request, $options) {
                    c::optional($this->factory)->recordRequestResponsePair(
                        (new CHTTP_Client_Request($request))->withData($options['laravel_data']),
                        new CHTTP_Client_Response($response)
                    );

                    return $response;
                });
            };
        };
    }

    /**
     * Build the stub handler.
     *
     * @return \Closure
     */
    public function buildStubHandler() {
        return function ($handler) {
            return function ($request, $options) use ($handler) {
                $response = ($this->stubCallbacks ?: c::collect())
                    ->map
                    ->__invoke((new CHTTP_Client_Request($request))->withData($options['laravel_data']), $options)
                    ->filter()
                    ->first();

                if (is_null($response)) {
                    return $handler($request, $options);
                }

                $response = is_array($response) ? CHTTP_Client::response($response) : $response;

                $sink = $options['sink'] ?? null;

                if ($sink) {
                    $response->then($this->sinkStubHandler($sink));
                }

                return $response;
            };
        };
    }

    /**
     * Get the sink stub handler callback.
     *
     * @param string $sink
     *
     * @return \Closure
     */
    protected function sinkStubHandler($sink) {
        return function ($response) use ($sink) {
            $body = $response->getBody()->getContents();

            if (is_string($sink)) {
                file_put_contents($sink, $body);

                return;
            }

            fwrite($sink, $body);
            rewind($sink);
        };
    }

    /**
     * Execute the "before sending" callbacks.
     *
     * @param \GuzzleHttp\Psr7\RequestInterface $request
     * @param array                             $options
     *
     * @return \Closure
     */
    public function runBeforeSendingCallbacks($request, array $options) {
        return c::tap($request, function ($request) use ($options) {
            $this->beforeSendingCallbacks->each->__invoke(
                (new CHTTP_Client_Request($request))->withData($options['laravel_data']),
                $options,
                $this
            );
        });
    }

    /**
     * Replace the given options with the current request options.
     *
     * @param array $options
     *
     * @return array
     */
    public function mergeOptions(...$options) {
        return array_replace_recursive(
            array_merge_recursive($this->options, carr::only($options, $this->mergableOptions)),
            ...$options
        );
    }

    /**
     * Register a stub callable that will intercept requests and be able to return stub responses.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function stub($callback) {
        $this->stubCallbacks = c::collect($callback);

        return $this;
    }

    /**
     * Toggle asynchronicity in requests.
     *
     * @param bool $async
     *
     * @return $this
     */
    public function async(bool $async = true) {
        $this->async = $async;

        return $this;
    }

    /**
     * Retrieve the pending request promise.
     *
     * @return null|\GuzzleHttp\Promise\PromiseInterface
     */
    public function getPromise() {
        return $this->promise;
    }

    /**
     * Dispatch the RequestSending event if a dispatcher is available.
     *
     * @return void
     */
    protected function dispatchRequestSendingEvent() {
        if ($dispatcher = c::optional($this->factory)->getDispatcher()) {
            $dispatcher->dispatch(new CHTTP_Client_Event_RequestSending($this->request));
        }
    }

    /**
     * Dispatch the ResponseReceived event if a dispatcher is available.
     *
     * @param \CHTTP_Client_Response $response
     *
     * @return void
     */
    protected function dispatchResponseReceivedEvent(CHTTP_Client_Response $response) {
        if (!($dispatcher = c::optional($this->factory)->getDispatcher())
            || !$this->request
        ) {
            return;
        }

        $dispatcher->dispatch(new CHTTP_Client_Event_ResponseReceived($this->request, $response));
    }

    /**
     * Dispatch the ConnectionFailed event if a dispatcher is available.
     *
     * @return void
     */
    protected function dispatchConnectionFailedEvent() {
        if ($dispatcher = c::optional($this->factory)->getDispatcher()) {
            $dispatcher->dispatch(new CHTTP_Client_Event_ConnectionFailed($this->request));
        }
    }

    /**
     * Set the client instance.
     *
     * @param \GuzzleHttp\Client $client
     *
     * @return $this
     */
    public function setClient(Client $client) {
        $this->client = $client;

        return $this;
    }

    /**
     * Create a new client instance using the given handler.
     *
     * @param callable $handler
     *
     * @return $this
     */
    public function setHandler($handler) {
        $this->client = $this->createClient(
            $this->pushHandlers(HandlerStack::create($handler))
        );

        return $this;
    }

    /**
     * Get the pending request options.
     *
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    public function getBodyFormat() {
        return $this->bodyFormat;
    }
}
