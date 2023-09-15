<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class CDebug_DebugBar_GuzzleProfiler_Middleware {
    /**
     * @var CDebug_DebugBar_GuzzleProfiler_ProfilerInterface
     */
    private $profiler;

    /**
     * Public constructor.
     *
     * @param CDebug_DebugBar_GuzzleProfiler_ProfilerInterface $profiler
     */
    public function __construct(CDebug_DebugBar_GuzzleProfiler_ProfilerInterface $profiler) {
        $this->profiler = $profiler;
    }

    /**
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler): callable {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            // Set starting time.
            $start = microtime(true);

            return $handler($request, $options)
                ->then(function (ResponseInterface $response) use ($start, $request) {
                    // After
                    $this->profiler->add($start, microtime(true), $request, $response);

                    return $response;
                }, function (GuzzleException $exception) use ($start, $request) {
                    $response = $exception instanceof RequestException ? $exception->getResponse() : null;
                    $this->profiler->add($start, microtime(true), $request, $response);

                    throw $exception;
                });
        };
    }
}
