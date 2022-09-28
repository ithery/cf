<?php

namespace React\Dns\Query;

use React\Promise\Timer;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

require_once __DIR__ . '/../../Promise/Timer/functions_include.php';
final class TimeoutExecutor implements ExecutorInterface {
    private $executor;

    private $loop;

    private $timeout;

    public function __construct(ExecutorInterface $executor, $timeout, LoopInterface $loop = null) {
        $this->executor = $executor;
        $this->loop = $loop ?: Loop::get();
        $this->timeout = $timeout;
    }

    public function query(Query $query) {
        return Timer\timeout($this->executor->query($query), $this->timeout, $this->loop)->then(null, function ($e) use ($query) {
            if ($e instanceof Timer\TimeoutException) {
                $e = new TimeoutException(sprintf('DNS query for %s timed out', $query->describe()), 0, $e);
            }

            throw $e;
        });
    }
}
