<?php

/**
 * Description of Process
 *
 * @author Hery
 */
class CDevSuite_Deploy_Process {

    /**
     *
     * @var CRemote_SSH
     */
    protected $ssh;

    /**
     *
     * @var CDevSuite_Deploy_Task
     */
    protected $task;

    /**
     *
     * @var string[]
     */
    protected $error;

    public static function fromSSH(CRemote_SSH $ssh, $task) {
        return new static($ssh, $task);
    }

    public function __construct($ssh, $task) {
        $this->ssh = $ssh;
        $this->task = $task;
        $this->error = [];
    }

    public function setTask(CDevSuite_Deploy_Task $task) {
        $this->task = $task;
        return $this;
    }

    public function setTimeout($timeout = null) {
        $this->ssh->connection()->setTimeout($timeout);
        return $this;
    }

    public function run($callback) {
        try {
            $this->ssh->run($this->task->script, $callback);
        } catch (Exception $ex) {
            $this->error[] = $ex->getMessage();
        }
        return $this;
    }

    public function getExitCode() {
        return count($this->error) == 0 ? CConsole::SUCCESS_EXIT : CConsole::FAILURE_EXIT;
    }

}
