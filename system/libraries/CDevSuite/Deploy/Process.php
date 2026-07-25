<?php

/**
 * Description of Process
 */
class CDevSuite_Deploy_Process {
    /**
     * @var CRemote_SSH
     */
    protected $ssh;

    /**
     * @var CDevSuite_Deploy_Task
     */
    protected $task;

    /**
     * @var string[]
     */
    protected $error;

    /**
     * Create a new Process instance from an SSH connection.
     *
     * @param CRemote_SSH           $ssh
     * @param CDevSuite_Deploy_Task $task
     *
     * @return static
     */
    public static function fromSSH(CRemote_SSH $ssh, $task) {
        return new static($ssh, $task);
    }

    /**
     * Create a new Process instance.
     *
     * @param CRemote_SSH           $ssh
     * @param CDevSuite_Deploy_Task $task
     *
     * @return void
     */
    public function __construct($ssh, $task) {
        $this->ssh = $ssh;
        $this->task = $task;
        $this->error = [];
    }

    /**
     * Set the task to be run.
     *
     * @param CDevSuite_Deploy_Task $task
     *
     * @return $this
     */
    public function setTask(CDevSuite_Deploy_Task $task) {
        $this->task = $task;
        return $this;
    }

    /**
     * Set the SSH connection timeout.
     *
     * @param int|null $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout = null) {
        $this->ssh->connection()->setTimeout($timeout);
        return $this;
    }

    /**
     * Run the task's script over SSH, recording any error.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function run($callback) {
        try {
            $this->ssh->run($this->task->script, $callback);
        } catch (Exception $ex) {
            $this->error[] = $ex->getMessage();
        }
        return $this;
    }

    /**
     * Get the exit code based on whether any error was recorded.
     *
     * @return int
     */
    public function getExitCode() {
        return count($this->error) == 0 ? CConsole::SUCCESS_EXIT : CConsole::FAILURE_EXIT;
    }
}
