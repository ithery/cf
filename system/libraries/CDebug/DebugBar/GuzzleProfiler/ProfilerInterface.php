<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface CDebug_DebugBar_GuzzleProfiler_ProfilerInterface {
    /**
     * @param float                               $start
     * @param float                               $end
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function add(float $start, float $end, RequestInterface $request, ResponseInterface $response = null): void;
}
