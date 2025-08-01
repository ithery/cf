<?php


class CServer_Process_FakeProcessSequence
{
    /**
     * The fake process results and descriptions.
     *
     * @var array
     */
    protected $processes = [];

    /**
     * Indicates that invoking this sequence when it is empty should throw an exception.
     *
     * @var bool
     */
    protected $failWhenEmpty = true;

    /**
     * The response that should be returned when the sequence is empty.
     *
     * @var \CServer_Process_Contract_ProcessResultInterface|\Illuminate\Process\FakeProcessDescription
     */
    protected $emptyProcess;

    /**
     * Create a new fake process sequence instance.
     *
     * @param  array  $processes
     * @return void
     */
    public function __construct(array $processes = [])
    {
        $this->processes = $processes;
    }

    /**
     * Push a new process result or description onto the sequence.
     *
     * @param  \CServer_Process_Contract_ProcessResultInterface|\CServer_Process_FakeProcessDescription|array|string  $process
     * @return $this
     */
    public function push($process)
    {
        $this->processes[] = $this->toProcessResult($process);

        return $this;
    }

    /**
     * Make the sequence return a default result when it is empty.
     *
     * @param  \CServer_Process_Contract_ProcessResultInterface|\CServer_Process_FakeProcessDescription|array|string  $process
     * @return $this
     */
    public function whenEmpty($process)
    {
        $this->failWhenEmpty = false;
        $this->emptyProcess = $this->toProcessResult($process);

        return $this;
    }

    /**
     * Convert the given result into an actual process result or description.
     *
     * @param  \CServer_Process_Contract_ProcessResultInterface|\CServer_Process_FakeProcessDescription|array|string  $process
     * @return \CServer_Process_Contract_ProcessResultInterface|\CServer_Process_FakeProcessDescription
     */
    protected function toProcessResult($process)
    {
        return is_array($process) || is_string($process)
                ? new CServer_Process_FakeProcessResult('', 0, $process)
                : $process;
    }

    /**
     * Make the sequence return a default result when it is empty.
     *
     * @return $this
     */
    public function dontFailWhenEmpty()
    {
        return $this->whenEmpty(new CServer_Process_FakeProcessResult);
    }

    /**
     * Indicate that this sequence has depleted all of its process results.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->processes) === 0;
    }

    /**
     * Get the next process in the sequence.
     *
     * @return \CServer_Process_Contract_ProcessResultInterface|\CServer_Process_FakeProcessDescription
     *
     * @throws \OutOfBoundsException
     */
    public function __invoke()
    {
        if ($this->failWhenEmpty && count($this->processes) === 0) {
            throw new OutOfBoundsException('A process was invoked, but the process result sequence is empty.');
        }

        if (! $this->failWhenEmpty && count($this->processes) === 0) {
            return c::value($this->emptyProcess ?? new CServer_Process_FakeProcessResult);
        }

        return array_shift($this->processes);
    }
}
