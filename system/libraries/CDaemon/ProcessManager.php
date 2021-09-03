<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 16, 2019, 11:59:56 AM
 */
class CDaemon_ProcessManager {
    /**
     * The length (in seconds) of the rolling window used to detect process churn
     */
    const CHURN_WINDOW = 120;

    /**
     * The number of failed processes within the CHURN_WINDOW required to trigger a fatal error
     */
    const CHURN_LIMIT = 5;

    /**
     * @var CDaemon_ServiceAbstract
     */
    public $service;

    /**
     * @var CDaemon_Process[]
     */
    public $processes = [];

    /**
     * Array of failed forks -- reaped within in expected_min_ttl
     *
     * @var array Numeric key, the value is the time the failure occurred
     */
    private $failures = [];

    public function __construct(CDaemon_ServiceAbstract $service) {
        $this->service = $service;
    }

    public function __destruct() {
        unset($this->service);
    }

    /**
     * Called on Construct or Init
     *
     * @return void
     */
    public function setup() {
        $this->service->on(CDaemon_ServiceAbstract::ON_IDLE, [$this, 'reap'], 30);
    }

    /**
     * Called on Destruct
     *
     * @return void
     */
    public function teardown() {
        if (!$this->service->isParent()) {
            return;
        }
        while ($this->count() > 0) {
            foreach ($this->processes() as $pid => $process) {
                if ($message = $process->stop()) {
                    $this->service->log($message);
                }
            }
            $this->reap(false);
            usleep(250000);
        }
        $this->reap(false);
    }

    /**
     * Return the number of processes, optional by $group
     *
     * @param $group
     *
     * @return int
     */
    public function count($group = null) {
        if ($group) {
            if (isset($this->processes[$group])) {
                return count($this->processes[$group]);
            } else {
                return 0;
            }
        }
        // Sum processes across all process groups
        $count = 0;
        foreach ($this->processes as $processGroup) {
            $count += count($processGroup);
        }
        return $count;
    }

    /**
     * The $processes array is hierarchical by process group. This will return a flat array of processes.
     *
     * @param null $group
     *
     * @return CDaemon_Process[]
     */
    public function processes($group = null) {
        if ($group) {
            if (isset($this->processes[$group])) {
                return $this->processes[$group];
            } else {
                return [];
            }
        }
        // List processes across all process groups
        $list = [];
        foreach ($this->processes as $process_group) {
            $list += $process_group;
        }
        return $list;
    }

    /**
     * Return a single process by its pid
     *
     * @param $pid
     *
     * @return CDaemon_Process
     */
    public function process($pid) {
        foreach ($this->processes as $processGroup) {
            if (isset($processGroup[$pid])) {
                return $processGroup[$pid];
            }
        }
        return null;
    }

    /**
     * Fork a new process, optionally within the supplied process $group.
     *
     * @param null $group
     *
     * @return bool|CDaemon_Process On failure, will return false. On success, a CDaemon_Process object will be
     *                              returned to the caller in the original (parent) process, and True will be returned to the caller in the
     *                              new (child) process.
     */
    public function fork($group = null) {
        $pid = pcntl_fork();
        switch ($pid) {
            case -1:
                // Parent Process - Fork Failed
                return false;
            case 0:
                // Child Process
                @pcntl_setpriority(1);
                $this->service->dispatch(CDaemon_Event::ON_FORK);
                return true;
            default:
                // Parent Process - Return the pid of the newly created Task
                $proc = new CDaemon_Process();
                $proc->pid = $pid;
                $proc->group = $group;
                if (!isset($this->processes[$group])) {
                    $this->processes[$group] = [];
                }
                $this->processes[$group][$pid] = $proc;
                return $proc;
        }
    }

    /**
     * Maintain the worker process map and notify the worker of an exited process.
     *
     * @param bool $block When true, method will block waiting for an exit signal
     *
     * @return void
     */
    public function reap($block = false) {
        $map = $this->processes();
        while (true) {
            $pid = pcntl_wait($status, ($block === true && $this->service->isParent()) ? null : WNOHANG);
            if (!$pid || !isset($map[$pid])) {
                break;
            }
            $alias = $map[$pid]->group;
            $process = $this->processes[$alias][$pid];
            $this->service->dispatch(CDaemon_Event::ON_REAP, [$process, $status]);
            unset($this->processes[$alias][$pid]);
            // Keep track of process churn -- failures within a processes min_ttl
            // If too many failures of new processes occur inside a given interval, that's a problem.
            // Raise a fatal error to prevent runaway process forking which can be very damaging to a server
            if ($this->service->isShutdown() || $process->runtime() >= $process->min_ttl) {
                continue;
            }
            foreach ($this->failures as $key => $failure_time) {
                if ($failure_time + self::CHURN_WINDOW < time()) {
                    unset($this->failures[$key]);
                }
            }
            if (count($this->failures) > self::CHURN_LIMIT) {
                $this->service->fatalError('Recently forked processes are continuously failing. See error log for additional details.');
            }
        }
    }
}
