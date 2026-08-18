<?php

class CConsole_Prompt_FormStep {
    /**
     * @var Closure
     */
    protected $condition;

    /**
     * @var Closure
     */
    protected $step;

    /**
     * @var null|string
     */
    public $name;

    /**
     * @var bool
     */
    protected $ignoreWhenReverting;

    /**
     * @param Closure    $step
     * @param bool|Closure $condition
     * @param null|string  $name
     * @param bool         $ignoreWhenReverting
     */
    public function __construct(Closure $step, $condition, $name, $ignoreWhenReverting) {
        $this->step = $step;
        $this->name = $name;
        $this->ignoreWhenReverting = $ignoreWhenReverting;

        $this->condition = is_bool($condition)
            ? function () use ($condition) {
                return $condition;
            }
            : $condition;
    }

    /**
     * Execute this step.
     *
     * @param array $responses
     * @param mixed $previousResponse
     *
     * @return mixed
     */
    public function run(array $responses, $previousResponse) {
        if (!$this->shouldRun($responses)) {
            return null;
        }

        return call_user_func($this->step, $responses, $previousResponse, $this->name);
    }

    /**
     * Whether the step should run based on the given condition.
     *
     * @param array $responses
     *
     * @return bool
     */
    protected function shouldRun(array $responses) {
        return call_user_func($this->condition, $responses);
    }

    /**
     * Whether this step should be skipped over when a subsequent step is reverted.
     *
     * @param array $responses
     *
     * @return bool
     */
    public function shouldIgnoreWhenReverting(array $responses) {
        if (!$this->shouldRun($responses)) {
            return true;
        }

        return $this->ignoreWhenReverting;
    }
}
