<?php

use GuzzleHttp\TransferStats;
use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\Assert as PHPUnit;
use GuzzleHttp\Psr7\Response as Psr7Response;

/**
 * @method \CHTTP_Client_PendingRequest accept(string $contentType)
 * @method \CHTTP_Client_PendingRequest acceptJson()
 * @method \CHTTP_Client_PendingRequest asForm()
 * @method \CHTTP_Client_PendingRequest asJson()
 * @method \CHTTP_Client_PendingRequest asMultipart()
 * @method \CHTTP_Client_PendingRequest async()
 * @method \CHTTP_Client_PendingRequest attach(string|array $name, string|resource $contents = '', null|string $filename = null, array $headers = [])
 * @method \CHTTP_Client_PendingRequest baseUrl(string $url)
 * @method \CHTTP_Client_PendingRequest beforeSending(callable $callback)
 * @method \CHTTP_Client_PendingRequest bodyFormat(string $format)
 * @method \CHTTP_Client_PendingRequest connectTimeout(int $seconds)
 * @method \CHTTP_Client_PendingRequest contentType(string $contentType)
 * @method \CHTTP_Client_PendingRequest dd()
 * @method \CHTTP_Client_PendingRequest dump()
 * @method \CHTTP_Client_PendingRequest retry(int $times, int $sleep = 0, ?callable $when = null, bool $throw = true)
 * @method \CHTTP_Client_PendingRequest sink(string|resource $to)
 * @method \CHTTP_Client_PendingRequest stub(callable $callback)
 * @method \CHTTP_Client_PendingRequest timeout(int $seconds)
 * @method \CHTTP_Client_PendingRequest withBasicAuth(string $username, string $password)
 * @method \CHTTP_Client_PendingRequest withBody(resource|string $content, string $contentType)
 * @method \CHTTP_Client_PendingRequest withCookies(array $cookies, string $domain)
 * @method \CHTTP_Client_PendingRequest withDigestAuth(string $username, string $password)
 * @method \CHTTP_Client_PendingRequest withHeaders(array $headers)
 * @method \CHTTP_Client_PendingRequest withMiddleware(callable $middleware)
 * @method \CHTTP_Client_PendingRequest withOptions(array $options)
 * @method \CHTTP_Client_PendingRequest withToken(string $token, string $type = 'Bearer')
 * @method \CHTTP_Client_PendingRequest withUserAgent(string $userAgent)
 * @method \CHTTP_Client_PendingRequest withoutRedirecting()
 * @method \CHTTP_Client_PendingRequest withoutVerifying()
 * @method array                        pool(callable $callback)
 * @method \CHTTP_Client_Response       delete(string $url, array $data = [])
 * @method \CHTTP_Client_Response       get(string $url, null|array|string $query = null)
 * @method \CHTTP_Client_Response       head(string $url, null|array|string $query = null)
 * @method \CHTTP_Client_Response       patch(string $url, array $data = [])
 * @method \CHTTP_Client_Response       post(string $url, array $data = [])
 * @method \CHTTP_Client_Response       put(string $url, array $data = [])
 * @method \CHTTP_Client_Response       send(string $method, string $url, array $options = [])
 *
 * @see \CHTTP_Client_PendingRequest
 */
class CHTTP_Client {
    use CTrait_Macroable {
        __call as macroCall;
    }

    public static $instance;

    /**
     * The event dispatcher implementation.
     *
     * @var null|\CEvent_DispatcherInterface
     */
    protected $dispatcher;

    /**
     * The stub callables that will handle requests.
     *
     * @var \CCollection
     */
    protected $stubCallbacks;

    /**
     * Indicates if the factory is recording requests and responses.
     *
     * @var bool
     */
    protected $recording = false;

    /**
     * The recorded response array.
     *
     * @var array
     */
    protected $recorded = [];

    /**
     * All created response sequences.
     *
     * @var array
     */
    protected $responseSequences = [];

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Create a new factory instance.
     *
     * @return void
     */
    public function __construct() {
        $this->dispatcher = CEvent::dispatcher();

        $this->stubCallbacks = c::collect();
    }

    /**
     * Create a new response instance for use during stubbing.
     *
     * @param array|string $body
     * @param int          $status
     * @param array        $headers
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public static function response($body = null, $status = 200, $headers = []) {
        if (is_array($body)) {
            $body = json_encode($body);

            $headers['Content-Type'] = 'application/json';
        }

        $response = new Psr7Response($status, $headers, $body);

        return class_exists(\GuzzleHttp\Promise\Create::class)
            ? \GuzzleHttp\Promise\Create::promiseFor($response)
            : \GuzzleHttp\Promise\promise_for($response);
    }

    /**
     * Get an invokable object that returns a sequence of responses in order for use during stubbing.
     *
     * @param array $responses
     *
     * @return \CHTTP_Client_ResponseSequence
     */
    public function sequence(array $responses = []) {
        return $this->responseSequences[] = new CHTTP_Client_ResponseSequence($responses);
    }

    /**
     * Register a stub callable that will intercept requests and be able to return stub responses.
     *
     * @param callable|array $callback
     *
     * @return $this
     */
    public function fake($callback = null) {
        $this->record();

        $this->recorded = [];

        if (is_null($callback)) {
            $callback = function () {
                return static::response();
            };
        }

        if (is_array($callback)) {
            foreach ($callback as $url => $callable) {
                $this->stubUrl($url, $callable);
            }

            return $this;
        }

        $this->stubCallbacks = $this->stubCallbacks->merge(c::collect([
            function ($request, $options) use ($callback) {
                $response = $callback instanceof Closure
                                ? $callback($request, $options)
                                : $callback;

                if ($response instanceof PromiseInterface) {
                    $options['on_stats'](new TransferStats(
                        $request->toPsrRequest(),
                        $response->wait(),
                    ));
                }

                return $response;
            },
        ]));

        return $this;
    }

    /**
     * Register a response sequence for the given URL pattern.
     *
     * @param string $url
     *
     * @return \CHTTP_Client_ResponseSequence
     */
    public function fakeSequence($url = '*') {
        return c::tap($this->sequence(), function ($sequence) use ($url) {
            $this->fake([$url => $sequence]);
        });
    }

    /**
     * Stub the given URL using the given callback.
     *
     * @param string                                                               $url
     * @param \CHTTP_Client_Response|\GuzzleHttp\Promise\PromiseInterface|callable $callback
     *
     * @return $this
     */
    public function stubUrl($url, $callback) {
        return $this->fake(function ($request, $options) use ($url, $callback) {
            if (!cstr::is(cstr::start($url, '*'), $request->url())) {
                return;
            }

            return $callback instanceof Closure || $callback instanceof CHTTP_Client_ResponseSequence
                ? $callback($request, $options)
                : $callback;
        });
    }

    /**
     * Begin recording request / response pairs.
     *
     * @return $this
     */
    protected function record() {
        $this->recording = true;

        return $this;
    }

    /**
     * Record a request response pair.
     *
     * @param \Illuminate\Http\Client\Request $request
     * @param \CHTTP_Client_Response          $response
     *
     * @return void
     */
    public function recordRequestResponsePair($request, $response) {
        if ($this->recording) {
            $this->recorded[] = [$request, $response];
        }
    }

    /**
     * Assert that a request / response pair was recorded matching a given truth test.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function assertSent($callback) {
        PHPUnit::assertTrue(
            $this->recorded($callback)->count() > 0,
            'An expected request was not recorded.'
        );
    }

    /**
     * Assert that the given request was sent in the given order.
     *
     * @param array $callbacks
     *
     * @return void
     */
    public function assertSentInOrder($callbacks) {
        $this->assertSentCount(count($callbacks));

        foreach ($callbacks as $index => $url) {
            $callback = is_callable($url) ? $url : function ($request) use ($url) {
                return $request->url() == $url;
            };

            PHPUnit::assertTrue($callback(
                $this->recorded[$index][0],
                $this->recorded[$index][1]
            ), 'An expected request (#' . ($index + 1) . ') was not recorded.');
        }
    }

    /**
     * Assert that a request / response pair was not recorded matching a given truth test.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function assertNotSent($callback) {
        PHPUnit::assertFalse(
            $this->recorded($callback)->count() > 0,
            'Unexpected request was recorded.'
        );
    }

    /**
     * Assert that no request / response pair was recorded.
     *
     * @return void
     */
    public function assertNothingSent() {
        PHPUnit::assertEmpty(
            $this->recorded,
            'Requests were recorded.'
        );
    }

    /**
     * Assert how many requests have been recorded.
     *
     * @param int $count
     *
     * @return void
     */
    public function assertSentCount($count) {
        PHPUnit::assertCount($count, $this->recorded);
    }

    /**
     * Assert that every created response sequence is empty.
     *
     * @return void
     */
    public function assertSequencesAreEmpty() {
        foreach ($this->responseSequences as $responseSequence) {
            PHPUnit::assertTrue(
                $responseSequence->isEmpty(),
                'Not all response sequences are empty.'
            );
        }
    }

    /**
     * Get a collection of the request / response pairs matching the given truth test.
     *
     * @param callable $callback
     *
     * @return \CCollection
     */
    public function recorded($callback = null) {
        if (empty($this->recorded)) {
            return c::collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return c::collect($this->recorded)->filter(function ($pair) use ($callback) {
            return $callback($pair[0], $pair[1]);
        });
    }

    /**
     * Create a new pending request instance for this factory.
     *
     * @return \CHTTP_Client_PendingRequest
     */
    protected function newPendingRequest() {
        return new CHTTP_Client_PendingRequest($this);
    }

    /**
     * Get the current event dispatcher implementation.
     *
     * @return null|\Illuminate\Contracts\Events\Dispatcher
     */
    public function getDispatcher() {
        return $this->dispatcher;
    }

    /**
     * Execute a method against a new pending request instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return c::tap($this->newPendingRequest(), function ($request) {
            $request->stub($this->stubCallbacks);
        })->{$method}(...$parameters);
    }
}
