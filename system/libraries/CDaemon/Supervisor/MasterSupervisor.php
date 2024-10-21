<?php

use Carbon\CarbonImmutable;

class CDaemon_Supervisor_MasterSupervisor implements CDaemon_Contract_PausableInterface, CDaemon_Contract_RestartableInterface, CDaemon_Contract_TerminableInterface {
    use CDaemon_Trait_ListenForSignals;

    /**
     * The name of the master supervisor.
     *
     * @var string
     */
    public $daemonClass;

    /**
     * The name of the master supervisor.
     *
     * @var string
     */
    public $name;

    /**
     * All of the supervisors managed.
     *
     * @var \CCollection
     */
    public $supervisors;

    /**
     * Indicates if the master supervisor process is working.
     *
     * @var bool
     */
    public $working = true;

    /**
     * The output handler.
     *
     * @var null|\Closure
     */
    public $output;

    /**
     * The callback to use to resolve master supervisor names.
     *
     * @var null|\Closure
     */
    public static $nameResolver;

    /**
     * Create a new master supervisor instance.
     *
     * @param null|string $daemonClass
     *
     * @return void
     */
    public function __construct($daemonClass = null) {
        $this->daemonClass = $daemonClass;
        $this->name = static::name();
        $this->supervisors = c::collect();

        $this->output = function () {
        };

        CDaemon_Supervisor::supervisorCommandQueue()->flush($this->commandQueue());
    }

    public function getDaemonClass() {
        return $this->daemonClass;
    }

    /**
     * Get the name for this master supervisor.
     *
     * @return string
     */
    public static function name() {
        static $token;

        if (!$token) {
            $token = cstr::slug(CF::appCode(), '_') . '-' . cstr::random(4);
        }

        return static::basename() . '-' . $token;
    }

    /**
     * Get the basename for the machine's master supervisors.
     *
     * @return string
     */
    public static function basename() {
        return static::$nameResolver
                        ? call_user_func(static::$nameResolver)
                        : cstr::slug(gethostname());
    }

    /**
     * Use the given callback to resolve master supervisor names.
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public static function determineNameUsing(Closure $callback) {
        static::$nameResolver = $callback;
    }

    /**
     * Terminate all current supervisors and start fresh ones.
     *
     * @return void
     */
    public function restart() {
        $this->working = true;

        $this->supervisors->each->terminateWithStatus(1);
    }

    /**
     * Pause the supervisors.
     *
     * @return void
     */
    public function pause() {
        $this->working = false;

        $this->supervisors->each->pause();
    }

    /**
     * Instruct the supervisors to continue working.
     *
     * @return void
     */
    public function continue() {
        $this->working = true;

        $this->supervisors->each->continue();
    }

    /**
     * Terminate this master supervisor and all of its supervisors.
     *
     * @param int $status
     *
     * @return void
     */
    public function terminate($status = 0) {
        $this->working = false;

        // First we will terminate all child supervisors so they will gracefully scale
        // down to zero. We'll also grab the longest expiration times of any of the
        // active supervisors so we know the maximum amount of time to wait here.
        $longest = CDaemon_Supervisor::supervisorRepository()->longestActiveTimeout();

        $this->supervisors->each->terminate();

        // We will go ahead and remove this master supervisor's record from storage so
        // another master supervisor could get started in its place without waiting
        // for it to really finish terminating all of its underlying supervisors.
        CDaemon_Supervisor::masterSupervisorRepository()->forget($this->name);

        $startedTerminating = CarbonImmutable::now();

        // Here we will wait until all of the child supervisors finish terminating and
        // then exit the process. We will keep track of a timeout value so that the
        // process does not get stuck in an infinite loop here waiting for these.
        while (count($this->supervisors->filter->isRunning())) {
            if (CarbonImmutable::now()->subSeconds($longest)->gte($startedTerminating)) {
                break;
            }

            sleep(1);
        }

        if (CF::config('daemon.supervisor.fast_termination')) {
            CCache::manager()->store()->forget('supervisor:terminate:wait');
        }

        $this->exit($status);
    }

    /**
     * Monitor the worker processes.
     *
     * @return void
     */
    public function monitor() {
        $this->ensureNoOtherMasterSupervisors();

        $this->listenForSignals();

        $this->persist();

        while (true) {
            sleep(1);

            $this->loop();
        }
    }

    /**
     * Ensure that this is the only master supervisor running for this machine.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function ensureNoOtherMasterSupervisors() {
        if (CDaemon_Supervisor::masterSupervisorRepository()->find($this->name) !== null) {
            throw new Exception('A master supervisor is already running on this machine.');
        }
    }

    /**
     * Perform a monitor loop.
     *
     * @return void
     */
    public function loop() {
        try {
            $this->processPendingSignals();

            $this->processPendingCommands();

            if ($this->working) {
                $this->monitorSupervisors();
            }

            $this->persist();
            c::event(new CDaemon_Supervisor_Event_MasterSupervisorLooped($this));
        } catch (Throwable $e) {
            CException::exceptionHandler()->report($e);
        }
    }

    /**
     * Handle any pending commands for the master supervisor.
     *
     * @return void
     */
    protected function processPendingCommands() {
        foreach (CDaemon_Supervisor::supervisorCommandQueue()->pending($this->commandQueue()) as $command) {
            CDaemon::log('Get Pending Command:' . $command->command);
            /** @see CDaemon_Supervisor_MasterSupervisorCommand_AddSupervisor */
            c::container($command->command)->process($this, $command->options);
        }
    }

    /**
     * "Monitor" all of the supervisors.
     *
     * @return void
     */
    protected function monitorSupervisors() {
        $this->supervisors->each->monitor();

        $this->supervisors = $this->supervisors->reject->dead;
    }

    /**
     * Persist information about the master supervisor instance.
     *
     * @return void
     */
    public function persist() {
        CDaemon_Supervisor::masterSupervisorRepository()->update($this);
    }

    /**
     * Get the process ID for this supervisor.
     *
     * @return int
     */
    public function pid() {
        return getmypid();
    }

    /**
     * Get the current memory usage (in megabytes).
     *
     * @return float
     */
    public function memoryUsage() {
        return memory_get_usage() / 1024 / 1024;
    }

    /**
     * Get the name of the command queue for the master supervisor.
     *
     * @return string
     */
    public static function commandQueue() {
        return 'master:' . static::name();
    }

    /**
     * Get the name of the command queue for the given master supervisor.
     *
     * @param null|string $name
     *
     * @return string
     */
    public static function commandQueueFor($name = null) {
        return $name ? 'master:' . $name : static::commandQueue();
    }

    /**
     * Set the output handler.
     *
     * @param \Closure $callback
     *
     * @return $this
     */
    public function handleOutputUsing(Closure $callback) {
        $this->output = $callback;

        return $this;
    }

    /**
     * Handle the given output.
     *
     * @param string $type
     * @param string $line
     *
     * @return void
     */
    public function output($type, $line) {
        call_user_func($this->output, $type, $line);
    }

    /**
     * Shutdown the supervisor.
     *
     * @param int $status
     *
     * @return void
     */
    protected function exit($status = 0) {
        $this->exitProcess($status);
    }

    /**
     * Exit the PHP process.
     *
     * @param int $status
     *
     * @return void
     */
    protected function exitProcess($status = 0) {
        exit((int) $status);
    }
}
