<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 15, 2019, 12:12:16 PM
 */
abstract class CDaemon_Worker_MediatorAbstract extends CDaemon_TaskAbstract {
    /**
     * The version is used in case formats change in the future.
     */
    const VERSION = 2.0;

    /**
     * Message Types.
     */
    const WORKER_CALL = 3;

    const WORKER_RUNNING = 2;

    const WORKER_RETURN = 1;

    /**
     * Call Statuses.
     */
    const UNCALLED = 0;

    const CALLED = 1;

    const RUNNING = 2;

    const RETURNED = 3;

    const CANCELLED = 4;

    const TIMEOUT = 10;

    /**
     * Forking Strategies.
     */
    const LAZY = 1;

    const MIXED = 2;

    const AGGRESSIVE = 3;

    /**
     * Map call statuses to the logical queue that messages will be written-to.
     *
     * @example When a call is in UNCALLED status, it should be written to the WORKER_CALL queue.
     *
     * @var array
     */
    public static $queueMap = [
        self::UNCALLED => self::WORKER_CALL,
        self::RUNNING => self::WORKER_RUNNING,
        self::RETURNED => self::WORKER_RETURN
    ];

    /**
     * @var CDaemon_ServiceAbstract
     */
    public $service;

    /**
     * Array of accumulated error counts. Error thresholds are localized and when reached will
     * raise a fatal error. Generally thresholds on workers are much lower than on the daemon process.
     *
     * @var array
     *
     * @todo should we move error counts to the mediator? And the Via object needs to report errors to the mediator? I think probably yes.
     */
    public $errorCounts = [
        'communication' => 0,
        'corruption' => 0,
        'catchall' => 0,
    ];

    /**
     * Error thresholds for (worker, parent). We want a certain tolerance of errors without restarting the application
     * and these settings can be tweaked per-application.
     *
     * Errors in worker processes have lower thresholds because it is trivial to replace a worker, and workers regularly
     * retire themselves anyway.
     *
     * Communication errors are anything related to a Via object's connection.
     * Corruption errors indicate the Via object was able to get or put a message but that it seemed improperly formatted
     * Catch-all for everything else.
     *
     * @var array
     */
    public $errorThresholds = [
        'communication' => [10, 50],
        'corruption' => [10, 25],
        'catchall' => [10, 25],
    ];

    /**
     * The forking strategy of the Worker.
     *
     * @example self::LAZY
     * Daemon Startup:      No processes are forked
     * Worker Method Call:  If existing process(es) are busy, fork another worker process for this call, up to the workers() limit.
     * In Lazy forking, processes are only forked as-needed
     * @example self::MIXED
     * Daemon Startup:      No processes are forked
     * Worker Method Call:  Fork maximum number of worker processes (as set via workers())
     * In Mixed forking, nothing is forked until the first method call but all forks are done simultaneously.
     * @example self::AGGRESSIVE
     * Daemon Startup:      All processes are forked up front
     * Worker Method Call:  Processes are forked as-needed to maintain the max number of available workers
     *
     * @var int
     *
     * @todo improve the intelligence behind the strategy selection to vary strategy by idle time in the daemon event loop, not the duration of the loop itself.
     */
    protected $forkingStrategy = self::MIXED;

    /**
     * @var CDaemon_Worker_ViaInterface
     */
    protected $via;

    /**
     * Methods available on the $object.
     *
     * @var array
     */
    protected $methods = [];

    /**
     * All Calls
     * A periodic garbage collection routine unsets ->args, ->return, leaving just the lightweight call meta-data behind.
     *
     * @var CDaemon_Worker_Call[]
     */
    protected $calls = [];

    /**
     * Call Counter - Used to assign keys in the $calls array
     * Note: Start at 1 because the 0 key is reserved for via classes to use as needed for metadata.
     *
     * @var int
     */
    protected $callCount = 1;

    /**
     * Array of Call ID's of calls currently running on one of the worker processes.
     * Calls are added when we receive a Running ack from a worker, and they're removed when the worker returns
     * or when the $timeout is reached.
     *
     * @var array
     */
    protected $runningCalls = [];

    /**
     * What is the alias this worker is set to on the Daemon?
     *
     * @var string
     */
    protected $alias = '';

    /**
     * The number of allowed concurrent workers.
     *
     * @example Set the worker count using $this->workers();
     *
     * @var int
     */
    protected $workers = 1;

    /**
     * How long, in seconds, can worker methods take before they should be killed?
     * Timeouts are an important tool in call processing guarantees: Workers that are killed or crash cannot notify the
     * daemon of the error. In these cases, the daemon only knows that the job was not acked as complete. In that way,
     * all errors are just timeouts. Your timeout handler will be called and your daemon will have the chance to retry
     * or otherwise handle the failure.
     *
     * Note: If you use your Timeout handler to retry a call, notice the $call->retries count that is kept for you. If your
     * call consistently leads to a fatal error in your worker processes, unlimited retries will result in continued worker
     * failure until the daemon reaches its error tolerance limit and tries to restart itself. Even then it's possible for the
     * queued call to persist until a manual intervention. By limiting retries the daemon can recover from a series of worker
     * fatal errors without affecting the application's stability.
     *
     * Note: There may be deviation in enforcement up to the length of your loop_interval. So if you set this ot "5" and
     * your loop interval is 2.5 second, workers may be allowed to run for up to 7.5 seconds before timing out. This
     * happens because timeouts and the onReturn and onTimeout calls are all handled inside the run() loop just before
     * your execute() method is called.
     *
     * @example set a Timeout using $this->timeout();
     *
     * @var float
     */
    protected $timeout = 60;

    /**
     * Callback that's called when a worker completes it's job.
     *
     * @example set a Return Handler using $this->onReturn();
     *
     * @var callable
     */
    protected $onReturn;

    /**
     * Callback that's called when a worker timeout is reached. See phpdoc comments on the $timeout property.
     *
     * @example set a Timeout Handler using $this->onTimeout();
     *
     * @var callable
     */
    protected $onTimeout;

    /**
     * The ID of this worker pool -- used to address shared IPC resources.
     *
     * @var int
     */
    protected $guid;

    /**
     * Flag to enable or disable worker auto-restart mechanism.
     *
     * @var bool
     */
    protected $autoRestart = true;

    /**
     * Return a valid callback for the supplied $method.
     *
     * @param $method
     */
    abstract protected function getCallback($method);

    public function __construct($alias, CDaemon_ServiceAbstract $service, CDaemon_Worker_ViaInterface $via) {
        $this->alias = $alias;
        $this->service = $service;
        $this->via = $via;
        $this->via->mediator = $this;
        $interval = $this->service->getLoopInterval();
        CDaemon::Log('Mediator Construct:Interval:' . $interval);
        switch (true) {
            case $interval > 2 || $interval === 0:
                $this->forkingStrategy = self::LAZY;

                break;
            case $interval > 1:
                $this->forkingStrategy = self::MIXED;

                break;
            default:
                $this->forkingStrategy = self::AGGRESSIVE;

                break;
        }
        $this->forkingStrategy = self::LAZY;
    }

    public function __destruct() {
        if (!$this->service->isParent()) {
            return;
        }
        // If there are no pending messages, release all shared resources.
        // If there are, then we want to preserve them so we can allow for daemon restarts without losing the call buffer
        if ($this->processCount() == 0) {
            $state = $this->via->state();
            if ($state['messages'] == 0) {
                $this->via->release();
            }
        }
        unset($this->via, $this->service);
    }

    /**
     * Make private & protected instance vars available for inspection
     * Primarily for access within the debugshell during mediator development.
     *
     * @param mixed $k
     */
    public function __get($k) {
        if (array_key_exists($k, get_object_vars($this))) {
            return $this->{$k};
        }

        return null;
    }

    public function checkEnvironment(array $errors = []) {
        if (function_exists('posix_kill') == false) {
            $errors[] = 'The POSIX Extension is Not Installed';
        }

        return $this->via->checkEnvironment($errors);
    }

    /**
     * Create an instance of CDaemon_DebugShell and pass in the current via object. Add appropriate closures and settings
     * for the desired commands, prompts, etc.
     *
     * @return void
     */
    public function debug() {
        //#
        //# Wrap the current $via object in a DebugShell mediator
        //#
        $this->via = new CDaemon_DebugShell($this->via);
        $this->via->service = $this->service;
        $this->via->setupShell();
        //# We'll use these in the many closures below..
        $that = $this;
        $shell = $this->via;
        $alias = $this->alias;
        //#
        //# Set callbacks to empower the indentation feature (for easy visual grouping)
        //# and the prompt-prefix (for prompt pid/alias/status identification)
        //#
        $this->via->indentCallback = function ($method, $args) use ($shell, $alias) {
            $callId = null;
            switch ($method) {
                case 'drop':
                    $callId = $args[0];

                    break;
                default:
                    if (isset($args[0]) && $args[0] instanceof CDaemon_Worker_Call) {
                        $callId = $args[0]->id;
                    }
            }
            if ($callId) {
                return $shell->incrementIndent($alias . $callId);
            }

            return 0;
        };
        $this->via->prompt_prefix_callback = function ($method, $args) use ($alias) {
            return sprintf('%s %s %s', $alias, getmypid(), (CDaemon::getRunningService()->isParent()) ? 'D' : 'W');
        };
        //#
        //# Set more specific and informative prompts for certain methods
        //#
        $this->via->prompts['put'] = function ($method, $args) use ($alias) {
            $statuses = [
                CDaemon_Worker_MediatorAbstract::UNCALLED => 'Daemon sending Call message to Worker',
                CDaemon_Worker_MediatorAbstract::RUNNING => 'Worker sending "running" ack message to Daemon',
                CDaemon_Worker_MediatorAbstract::RETURNED => 'Worker sending "return" ack message to Daemon',
            ];
            if (!$args[0] instanceof CDaemon_Worker_Call) {
                return false;
            }

            return "[Call {$args[0]->id}] " . $statuses[$args[0]->status];
        };
        $this->via->prompts['drop'] = function ($method, $args) use ($alias) {
            return "[Call {$args[0]}] Garbage-collect this call?";
        };
        $this->via->prompts['state'] = 'Load IPC state details?';
        //#
        //# Add any methods to the blacklist that shouldn't trigger a debug prompt. Mostly ones that would just be noise.
        //#
        $this->via->blacklist = [
            'get', 'setup',
        ];
        //#
        //# Add additional command parsers
        //#
        $parsers = [];
        $parsers[] = [
            'regex' => '/^call ([A-Z_0-9]+) (.*)?/i',
            'command' => 'call [f] [a,b..]',
            'description' => 'Call a worker\'s method in the local process with any additional arguments you supply after the method name.',
            'closure' => function ($matches, $printer) use ($that) {
                // Extract any args that may have been passed.
                // They can be delimited by a comma or space
                $args = [];
                if (count($matches) == 3) {
                    $args = explode(' ', str_replace(',', ' ', $matches[2]));
                }
                // If this is an object mediator, use inline() to grab an instance of the underlying worker object.
                // Otherwise use the mediator itself as the call context.
                $context = ($that instanceof CDaemon_Worker_MediatorObject) ? $that->inline() : $that;
                $function = [$context, $matches[1]];
                if (!is_callable($function)) {
                    $printer('Function Not Callable!');

                    return false;
                }
                $printer("Calling Function {$matches[1]}()...");
                $return = call_user_func_array($function, $args);
                if (is_scalar($return)) {
                    $printer("Return: ${return}", 100);
                } else {
                    $printer('Return: [' . gettype($return) . ']');
                }

                return false;
            }
        ];
        $parsers[] = [
            'regex' => '/^show (\d+)/i',
            'command' => 'show [n]',
            'description' => 'Display the Nth item in local memory - from the $this->calls array',
            'closure' => function ($matches, $printer) use ($that) {
                if (!is_array($that->calls)) {
                    $printer('No Calls In Memory', PHP_EOL);

                    return;
                }
                if (isset($that->calls[$matches[1]])) {
                    $printer(print_r(@$that->calls[$matches[1]], true));
                } else {
                    $printer('Item Does Not Exist');
                }
            }
        ];
        $parsers[] = [
            'regex' => '/^types$/i',
            'command' => 'types',
            'description' => 'Display a table of message types and statuses so you can figure out what they mean.',
            'closure' => function ($matches, $printer) use ($that) {
                $out = [];
                $out[] = 'Message Types:';
                $out[] = '1     Worker Sending "onReturn" message to the Daemon';
                $out[] = '2     Worker Notifying Daemon that it received the Call message and will now begin work.';
                $out[] = '3     Daemon sending a Call message to the Worker';
                $out[] = '';
                $out[] = 'Statuses:';
                $out[] = '0     Uncalled';
                $out[] = '1     Called';
                $out[] = '2     Running';
                $out[] = '3     Returned';
                $out[] = '4     Cancelled';
                $out[] = '10    Timeout';
                $out[] = '';
                $printer(implode(PHP_EOL, $out));
            }
        ];
        $parsers[] = [
            'regex' => '/^status$/i',
            'command' => 'call [f] [a,b..]',
            'description' => 'Display current process details.',
            'closure' => function ($matches, $printer) use ($that) {
                if ($this->service->isParent()) {
                    $processes = $that->processes();
                    $out = [];
                    $out[] = '';
                    $out[] = 'Daemon Process';
                    $out[] = 'Alias: ' . $that->alias;
                    $out[] = 'IPC ID: ' . $that->guid;
                    $out[] = 'Workers: ' . count($processes);
                    $out[] = 'Max Workers: ' . $that->workers;
                    $out[] = 'Running Jobs: ' . count($that->runningCalls);
                    $out[] = '';
                    $out[] = 'Processes:';
                    if ($processes) {
                        foreach ($processes as $pid => $process) {
                            $out[] = "[${pid}] Runtime: " . $process->runtime();
                        }
                    } else {
                        $out[] = 'None';
                    }
                    $out[] = '';
                    $printer(implode(PHP_EOL, $out));
                } else {
                    $out = [];
                    $out[] = '';
                    $out[] = 'Worker Process';
                    $out[] = 'Alias: ' . $that->alias;
                    $out[] = 'IPC ID: ' . $that->id;
                    $out[] = '';
                    $printer(implode(PHP_EOL, $out));
                }
            }
        ];
        $this->via->loadParsers($parsers);
    }

    /**
     * If the daemon is in debug-mode, you can set breakpoints in your worker code. If "continue" commands were passed
     * in the debug shell, breakpoint will return true, otherwise false. It's up to you to handle that behavior in your app.
     * Note: If the daemon is not in debug mode, it will always just return true.
     *
     * @param string $prompt The prompt to display
     * @param int    $indent The indentation level to use for the prompt, useful for grouping like-prompts together
     *
     * @return bool
     */
    public function breakpoint($prompt = '', $indent = 0) {
        $this->log($prompt);
        if (!$this->service->isDebugWorkers()) {
            return true;
        }
        $call = debug_backtrace();
        $call = $call[1];
        $method = sprintf('%s::%s', $call['class'], $call['function']);
        $this->via->prompts[$method] = $prompt;
        if ($indent) {
            $indent = $this->via->incrementIndent($this->alias . $indent);
            $tmp = $this->via->indent_callback;
            $this->via->indent_callback = function () use ($indent) {
                return $indent;
            };
        }
        $return = true;
        if ($this->via instanceof CDaemon_DebugShell) {
            $return = $this->via->prompt($method, $call['args']);
        }
        if (isset($tmp)) {
            $this->via->indentCallback = $tmp;
        }

        return $return;
    }

    public function setup() {
        // This class implements both the Task and the Plugin interfaces. Like plugins, this setup() method will be
        // called in the parent process during application init. And like tasks, this setup() method will be called right
        // after the process is forked.
        $that = $this;
        if ($this->service->isParent()) {
            // Use the ftok() method to create a deterministic memory address.
            // This is a bit ugly but ftok needs a filesystem path so we give it one using the daemon filename and
            // current worker alias.
            $tmp = sys_get_temp_dir();
            $ftok = sprintf($tmp . '/%s_%s', str_replace('/', '_', $this->service->getServiceName()), $this->alias);
            if (!touch($ftok)) {
                $this->fatalError("Unable to create Worker ID. ftok() failed. Could not write to {$tmp} directory at {$ftok}");
            }
            $this->guid = ftok($ftok, $this->alias[0]);
            @unlink($ftok);
            if ($this->guid == -1) {
                $this->fatalError("Unable to create Worker ID. ftok() failed. Unexpected return value: {$this->guid}");
            }
            $this->via->setup();
            $this->via->purge();
            if ($this->service->isDebugWorkers()) {
                $this->debug();
            }
            $this->service->on(CDaemon_ServiceAbstract::ON_PREEXECUTE, [$this, 'run']);
            $this->service->on(CDaemon_ServiceAbstract::ON_IDLE, [$this, 'garbageCollector'], ceil(120 / ($this->workers * 0.5)));  // Throttle the garbage collector
            $this->service->on(CDaemon_ServiceAbstract::ON_SIGNAL, [$this, 'dump'], null, function ($args) {
                return $args[0] == SIGUSR1;
            });
            $this->fork();
        } else {
            unset($this->calls, $this->runningCalls, $this->onReturn, $this->onTimeout, $this->callCount);
            $this->calls = $this->callCount = $this->runningCalls = [];
            $this->via->setup();
            $eventRestart = function () use ($that) {
                $that->log('Restarting Worker Process...');
            };
            $this->service->on(CDaemon_ServiceAbstract::ON_SIGNAL, $eventRestart, null, function ($args) {
                return $args[0] == SIGUSR1;
            });
            call_user_func($this->getCallback('setup'));
            $this->log('Worker Process Started');
        }
    }

    public function teardown() {
        // Required to satisfy Core_ITask
    }

    /**
     * Satisfy the Core_ITask interface.
     *
     * @return string
     */
    public function group() {
        return $this->alias;
    }

    /**
     * Fork an appropriate number of daemon processes. Looks at the daemon loop_interval to determine the optimal
     * forking strategy: If the loop is very tight, we will do all the forking up-front. For longer intervals, we will
     * fork as-needed. In the middle we will avoid forking until the first call, then do all the forks in one go.
     *
     * @return mixed
     */
    protected function fork() {
        $processes = $this->processCount();
        if ($this->workers <= $processes) {
            return;
        }
        $forks = 0;
        switch ($this->forkingStrategy) {
            case self::LAZY:
                $state = $this->via->state();
                $forks = 1;
                if ($processes > count($this->runningCalls)) {
                    $forks = 0;
                }

                break;
            case self::MIXED:
                if ($this->callCount == 0) {
                    $forks = 0;

                    break;
                }
                // no break
            case self::AGGRESSIVE:
            default:
                $forks = $this->workers - $processes;

                break;
        }
        // Handle a case where we have a new process in a LAZY or MIXED strategy with pending messages on the queue
        if ($forks == 0) {
            if (!isset($state)) {
                $state = $this->via->state();
            }
            if ($this->callCount == 0 && $state['messages'] > 0) {
                $forks = 1;
            }
        }
        if ($forks && !$this->breakpoint("Forking {$forks} New Worker Processes")) {
            return false;
        }
        $this->breakpoint('Forking success');
        $errors = 0;
        for ($i = 0; $i < $forks; $i++) {
            // A Core_Lib_Process object will be returned from the task() method.
            // Set correct min_ttl and timeout values so the ProcessManager can do its job.
            if ($process = $this->service->task($this)) {
                $process->timeout = $this->timeout;
                $process->min_ttl = 30;
                $errors = 0;

                continue;
            }
            // If the forking failed, we can retry a few times and then fatal-error
            // The most common reason this could happen is the PID table gets full or the machine runs out of memory.
            if ($errors++ < 3) {
                $i--;

                continue;
            }
            $this->fatalError('Could Not Fork: See PHP error log for an error code and more information.');
        }
    }

    /**
     * Called in each iteration of your daemon's event loop. Listens for worker Acks and enforces timeouts when applicable.
     * Note: Called only in the parent (daemon) process, attached to the Core_Daemon::ON_PREEXECUTE event.
     *
     * @return void
     */
    public function run() {
        if (empty($this->calls)) {
            return;
        }

        try {
            // If there are any callbacks registered (onReturn, onTimeout, etc), we will pass
            // the call object and this $logger closure to them
            $that = $this;
            $logger = function ($message) use ($that) {
                $that->log($message);
            };
            while ($call = $this->via->get(self::WORKER_RUNNING)) {
                if (!isset($this->calls[$call->id])) {
                    $this->log('Warning: Message received that was enqueued by a previous instance of this application.');
                    $this->log('         Consider purging any pending messages using whatever process exists for your selected queue provider');

                    continue;
                }
                $this->runningCalls[$call->id] = true;
                // It's possible the process exited after sending this ack, ensure it's still valid.
                if ($this->process($call->pid)) {
                    $this->process($call->pid)->job = $call->id;
                }
                $this->log('Job ' . $call->id . ' Is Running');
            }
            while ($call = $this->via->get(self::WORKER_RETURN)) {
                if (!isset($this->calls[$call->id])) {
                    $this->log('Warning: Message received that was enqueued by a previous instance of this application.');
                    $this->log('         Consider purging any pending messages using whatever process exists for your selected queue provider');

                    continue;
                }
                unset($this->runningCalls[$call->id]);
                if ($this->process($call->pid)) {
                    $this->process($call->pid)->job = $call->id;
                }
                $onReturn = $this->onReturn;
                if (is_callable($onReturn)) {
                    call_user_func($onReturn, $call, $logger);
                } else {
                    $this->log('No onReturn Callback Available');
                }
                $this->log('Job ' . $call->id . ' Is Complete');
            }
            // Enforce Timeouts
            // Timeouts will either be simply that the worker is taking longer than expected to return the call,
            // or the worker actually fatal-errored and killed itself.
            if ($this->timeout > 0) {
                foreach (array_keys($this->runningCalls) as $callId) {
                    $call = $this->calls[$callId];
                    if ($call->runtime() > $this->timeout) {
                        if (!$this->breakpoint("[{$this->alias} Call {$callId}] Enforcing timeout at runtime: " . $call->runtime(), $callId - 1)) {
                            continue;
                        }
                        $this->log("Enforcing Timeout on Call ${callId} in pid " . $call->pid);
                        if ($this->process($call->pid) instanceof CDaemon_Process) {
                            $this->process($call->pid)->kill();
                        }
                        $call->timeout();
                        unset($this->runningCalls[$callId]);
                        $onTimeout = $this->onTimeout;
                        if (is_callable($onTimeout)) {
                            call_user_func($onTimeout, $call, $logger);
                        }
                    }
                }
            }
            // If we've killed all our processes -- either timeouts or maybe they fatal-errored -- and we have pending
            // calls in the queue, fork()
            if ($this->processCount() == 0) {
                $state = $this->via->state();
                if ($state['messages'] > 0) {
                    $this->fork();
                }
            }
        } catch (Exception $e) {
            $this->log(__METHOD__ . ' Failed: ' . $e->getMessage(), true);
        }
    }

    /**
     * Starts the event loop in the Forked process that will listen for messages
     * Note: Runs only in the worker (forked) process.
     *
     * @return void
     */
    public function start() {
        // Define automatic restart intervals. We want to add some entropy to avoid having all worker processes
        // in a pool restart at the same time. Use a very crude technique to create a random number along a normal distribution.
        $entropy = round((mt_rand(-1000, 1000) + mt_rand(-1000, 1000) + mt_rand(-1000, 1000)) / 100, 0);
        $recycle = false;
        while (!$this->service->isParent() && !$this->service->isParent() && !$recycle) {
            // Give the CPU a break - Sleep for 1/20 a second.
            usleep(50000);
            if ($this->autoRestart) {
                $max_jobs = $this->callCount++ >= (25 + $entropy);
                $min_runtime = $this->service->runtime() >= (60 * 5);
                $max_runtime = $this->service->runtime() >= (60 * 30 + $entropy * 10);
                $recycle = ($max_runtime || $min_runtime && $max_jobs);
            }
            if (mt_rand(1, 5) == 1) {
                $this->garbageCollector();
            }

            if ($call = $this->via->get(self::WORKER_CALL, true)) {
                try {
                    // If the current via supports it, calls can be cancelled while they are enqueued
                    if ($call->status == self::CANCELLED) {
                        $this->log("Call {$call->id} Cancelled By Mediator -- Skipping...");

                        continue;
                    }
                    $alias = ($this instanceof CDaemon_Worker_MediatorObject) ? $call->method : $this->alias;
                    if (!$this->breakpoint(sprintf('[Call %s] Calling method in worker', $call->id, $alias), $call->id)) {
                        $call->cancelled();

                        continue;
                    }
                    $call->running();
                    if (!$this->via->put($call)) {
                        $this->log("Call {$call->id} Could Not Ack Running.");
                    }
                    $call->returned(call_user_func_array($this->getCallback($call->method), $call->args));
                    if (!$this->via->put($call)) {
                        $this->log("Call {$call->id} Could Not Ack Complete.");
                    }
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        $this->log('Recycling Worker...');
    }

    /**
     * Mediate all calls to methods on the contained $object and pass them to instances of $object running in the background.
     *
     * @param CDaemon_Worker_Call $call
     *
     * @return int A unique-per-process identifier for the call OR false on error. That ID can be used to interact
     *             with the call, eg checking the call status.
     */
    protected function call(CDaemon_Worker_Call $call) {
        $this->calls[$call->id] = $call;
        $alias = ($this instanceof CDaemon_Worker_MediatorObject) ? $call->method : $this->alias;
        if (!$this->breakpoint(sprintf('[Call %s] Call to %s()', $call->id, $alias), $call->id)) {
            $call->cancelled();

            return false;
        }

        try {
            if ($this->via->put($call)) {
                $call->called();
                $this->fork();

                return $call->id;
            }
        } catch (Exception $e) {
            $this->log('Call Failed: ' . $e->getMessage(), true);
        }
        // The call failed -- args could be big so trim it back proactively, leaving
        // the call metadata the same way the GC process works
        $call->args = null;

        return false;
    }

    /**
     * Intercept method calls on worker objects and pass them to the worker processes.
     *
     * @param $method
     * @param $args
     *
     * @throws Exception
     *
     * @return bool
     */
    public function __call($method, $args) {
        if (!in_array($method, $this->methods)) {
            throw new Exception(__METHOD__ . " Failed. Method `{$method}` is not callable.");
        }
        $this->callCount++;

        return $this->call(new CDaemon_Worker_Call($this->callCount, $method, $args));
    }

    /**
     * Return the requested call from the local call cache if it exists.
     *
     * @param mixed $callId
     *
     * @return CDaemon_Worker_Call
     */
    public function getStruct($callId) {
        if (isset($this->calls[$callId])) {
            return $this->calls[$callId];
        }

        return null;
    }

    /**
     * Set the supplied $call into the local call cache, doing any merging with a previously-cached version is necessary.
     *
     * @param CDaemon_Worker_Call $call
     */
    public function setStruct(CDaemon_Worker_Call $call) {
        if (isset($this->calls[$call->id])) {
            $this->calls[$call->id] = $call->merge($this->calls[$call->id]);
        } else {
            $this->calls[$call->id] = $call;
        }
    }

    /**
     * Periodically garbage-collect call structs: Keep the metadata but remove the (potentially large) args and return values
     * The parent will also ensure any GC'd items are removed from shared memory though in normal operation they're deleted when they return
     * Essentially a mark-and-sweep strategy. The garbage collector will also do some analysis on calls that seem frozen
     * and attempts to retry them when appropriate.
     *
     * @return void
     */
    public function garbageCollector() {
        if (!$this->breakpoint('Run Garbage Collector')) {
            return;
        }
        $called = [];
        foreach ($this->calls as $callId => &$call) {
            if ($call->gc() && $this->service->isParent()) {
                $this->via->drop($callId);
            }
            if ($call->status == self::CALLED) {
                $called[] = $callId;
            }
        }
        unset($call);
        if (!$this->service->isParent() || count($called) == 0) {
            return;
        }
        // We need to determine if we have any "dropped calls" in CALLED status. This could happen in a few scenarios:
        // 1) There was a silent message-queue failure and the item was never presented to workers.
        // 2) A worker received the message but fatal-errored before acking.
        // 3) A worker received the message but a message queue failure prevented the acks being sent.
        // Look at all the jobs recently acked and determine which of them was called first. Get the time of that call as the $cutoff.
        // Any Calls in CALLED status that were called prior to that $cutoff have apparently been dropped and will be requeued.
        $cutoff = $this->calls[$this->callCount]->time[self::CALLED];
        foreach ($this->processes() as $process) {
            if ($process->job === null && time() - $process->microtime < 30) {
                return;
            } // Give processes time to ack their first job
            if ($process->job !== null) {
                $cutoff = min($cutoff, $this->calls[$process->job]->time[self::CALLED]);
            }
        }
        foreach ($called as $callId) {
            $call = $this->calls[$callId];
            if ($call->time[self::CALLED] > $cutoff) {
                continue;
            }
            // If there's a retry count above our threshold log and skip to avoid endless requeueing
            if ($call->retries > 3) {
                $call->cancelled();
                $this->error("Dropped Call. Requeue threshold reached. Call {$call->id} will not be requeued.");

                continue;
            }
            // Requeue the message. If somehow the original message is still out there the worker will compare timestamps
            // and mark the original call as CANCELLED.
            $this->log("Dropped Call. Requeuing Call {$call->id} To `{$call->method}`");
            $call->retry();
            $this->call($call);
        }
    }

    /**
     * Report an error. Keep a count of error types and act appropriately when thresholds have been met.
     *
     * @param $type
     *
     * @return void
     */
    public function countError($type) {
        $this->errorCounts[$type]++;
        if ($this->errorCounts[$type] > $this->errorThresholds[$type][(int) $this->service->isParent()]) {
            $this->fatalError("IPC '${type}' Error Threshold Reached");
        }
    }

    /**
     * Increase back-off delays in an exponential way up to a certain plateau.
     *
     * @param $delay
     * @param $try
     */
    public function backoff($delay, $try) {
        return $delay * pow(2, min(max($try, 1), 8)) - $delay;
    }

    /**
     * If your worker object implements an execute() method, it can be called in the daemon using $this->MyAlias().
     *
     * @return bool
     */
    public function __invoke() {
        return $this->__call('execute', func_get_args());
    }

    /**
     * Helper function to retrieve the selected process from the Core_Daemon process registry.
     *
     * @param $pid
     *
     * @return Core_Lib_Process
     */
    public function process($pid) {
        if (isset($this->service->ProcessManager)) {
            return $this->service->ProcessManager->process($pid);
        }

        return null;
    }

    /**
     * Helper function to retrieve all of this workers processes from the Core_Daemon process registry.
     *
     * @return Core_Lib_Process[]
     */
    public function processes() {
        if (isset($this->service->ProcessManager)) {
            return $this->service->ProcessManager->processes($this->alias);
        }

        return [];
    }

    /**
     * Helper function to retrieve the process count from the ProcessManager for this worker.
     *
     * @return mixed
     */
    public function processCount() {
        $processManager = $this->service->getPlugin('ProcessManager');
        if ($processManager != null) {
            return $processManager->count($this->alias);
        }

        return 0;
    }

    /**
     * Retrieves the number of worker processes that are currently running.
     *
     * @return number
     */
    public function runningCount() {
        return count($this->runningCalls);
    }

    /**
     * Dump runtime stats in tabular fashion to the log.
     *
     * @return void
     */
    public function dump() {
        $status_labels = [
            self::UNCALLED => 'Uncalled',
            self::CALLED => 'Called',
            self::RUNNING => 'Running',
        ];
        // Compute the raw duration data for each call, grouped by method name and status
        // (See how long we were in CALLED status waiting to run, how long we were RUNNING, etc)
        $durations = [];
        foreach ($this->calls as $call) {
            if (!isset($durations[$call->method])) {
                $durations[$call->method] = [];
            }
            foreach ([self::CALLED, self::RUNNING] as $status) {
                if (!isset($durations[$call->method][$status])) {
                    $durations[$call->method][$status] = [];
                }
                if (isset($call->time[$status + 1])) {
                    $durations[$call->method][$status][] = max(round($call->time[$status + 1] - $call->time[$status], 5), 0);
                }
            }
        }
        // Write out the header
        // Then write out the data table with an indent
        $out = [];
        $out[] = '---------------------------------------------------------------------------------------------------';
        $out[] = 'Worker Runtime Statistics';
        $out[] = '---------------------------------------------------------------------------------------------------';
        $out[] = '';
        $this->log(implode("\n", $out));
        $out = [];
        $out[] = 'Method Duration      Status           Mean     Median      Count';
        $out[] = '================================================================';
        foreach ($durations as $method => $method_data) {
            foreach ($method_data as $status => $status_data) {
                $mean = $median = 0;
                sort($status_data);
                if ($count = count($status_data)) {
                    $mean = round(array_sum($status_data) / $count, 5);
                    $median = round($status_data[intval($count / 2)], 5);
                }
                $out[] = sprintf(
                    '%s %s %s %s %s',
                    str_pad(substr($method, 0, 20), 20, ' ', STR_PAD_RIGHT),
                    str_pad($status_labels[$status], 10, ' ', STR_PAD_RIGHT),
                    str_pad(number_format($mean, 5, '.', ''), 10, ' ', STR_PAD_LEFT),
                    str_pad(number_format($median, 5, '.', ''), 10, ' ', STR_PAD_LEFT),
                    str_pad(number_format($count, 0), 10, ' ', STR_PAD_LEFT)
                );
            }
        }
        $out[] = '';
        $out[] = 'Error Type      Count';
        $out[] = '=====================';
        foreach ($this->errorCounts as $type => $count) {
            $out[] = sprintf(
                '%s %s',
                str_pad(ucfirst($type), 15),
                str_pad(number_format($count, 0), 5, ' ', STR_PAD_LEFT)
            );
        }
        $this->log(implode("\n", $out), 1);
        $this->log('');
    }

    /**
     * Write do the Daemon's event log.
     *
     * Part of the Worker API - Use from your workers to log events to the Daemon event log
     *
     * @param $message
     * @param mixed $indent
     *
     * @return void
     */
    public function log($message, $indent = 0) {
        $this->service->log("${message}", $this->alias, $indent);
    }

    /**
     * Dispatch ON_ERROR event and write an error message to the Daemon's event log.
     *
     * Part of the Worker API - Use from your workers to log an error message.
     *
     * @param $message
     *
     * @return void
     */
    public function error($message) {
        $this->service->error("${message}", $this->alias);
    }

    /**
     * Dispatch ON_ERROR event, write an error message to the event log, and restart the worker.
     *
     * Part of the Worker API - Use from your worker to log a fatal error message and restart the current process.
     *
     * @param $message
     *
     * @return void
     */
    public function fatalError($message) {
        if ($this->service->isParent()) {
            $this->service->fatalError("Fatal Error: ${message}", $this->alias);
        } else {
            $this->service->fatalError("Fatal Error: ${message}\nWorker process will restart", $this->alias);
        }
    }

    /**
     * Access daemon properties from within your workers.
     *
     * Part of the Worker API - Use from your worker to access data set on your Daemon class
     *
     * example [inside a worker class] $this->mediator->service('dbconn');
     * example [inside a worker class] $ini = $this->mediator->service('ini'); $ini['database']['password']
     *
     * @param $property
     *
     * @return mixed
     */
    public function daemon($property) {
        if (isset($this->service->{$property}) && !is_callable($this->service->{$property})) {
            return $this->service->{$property};
        }

        return null;
    }

    /**
     * Re-run a previous call by passing in the call's struct.
     * Note: When calls are re-run a retry=1 property is added, and that is incremented for each re-call. You should check
     * that value to avoid re-calling failed methods in an infinite loop.
     *
     * Part of the Daemon API - Use from your daemon to retry a given call
     *
     * example You set a timeout handler using onTimeout. The worker will pass the timed-out call to the handler as a
     * stdClass object. You can re-run it by passing the object here.
     *
     * @param CDaemon_Worker_Call $call
     *
     * @return bool
     */
    public function retry(CDaemon_Worker_Call $call) {
        if (empty($call->method)) {
            throw new Exception(__METHOD__ . ' Failed. A valid call struct is required.');
        }
        $this->log("Retrying Call {$call->id} To `{$call->method}`");
        $call->retry();

        return $this->call($call);
    }

    /**
     * Determine the status of a given call. Call ID's are returned when a job is called. Important to note that
     * call ID's are only unique within this worker and this execution.
     *
     * Part of the Daemon API - Use from your daemon to determine the status of a given call
     *
     * @param int $callId
     *
     * @return int Return a status int - See status constants in this class
     */
    public function status($callId) {
        if (isset($this->calls[$callId])) {
            return $this->calls[$callId]->status;
        }

        return null;
    }

    /**
     * Set a callable that will called whenever a timeout is enforced on a worker.
     * The offending $call stdClass will be passed-in. Can be passed to retry() to re-try the call. Will have a
     * `retries=N` property containing the number of times it's been sent thru retry().
     *
     * Part of the Daemon API - Use from your daemon to set a Timeout handler
     *
     * @param callable $onTimeout
     *
     * @throws Exception
     */
    public function onTimeout($onTimeout) {
        if (!is_callable($onTimeout)) {
            throw new Exception(__METHOD__ . ' Failed. Callback or Closure expected.');
        }
        $this->onTimeout = $onTimeout;
    }

    /**
     * Set a callable that will be called when a worker method completes.
     * The $call stdClass will be passed-in -- with a `return` property.
     *
     * Part of the Daemon API - Use from your daemon to set a Return handler
     *
     * @param callable $onReturn
     *
     * @throws Exception
     */
    public function onReturn($onReturn) {
        if (!is_callable($onReturn)) {
            throw new Exception(__METHOD__ . ' Failed. Callback or Closure expected.');
        }
        $this->onReturn = $onReturn;
    }

    /**
     * Set the timeout for methods called on this worker. When a timeout happens, the onTimeout() callback is called.
     *
     * Part of the Daemon API - Use from your daemon to set a timeout for all worker calls.
     *
     * @param $timeout
     *
     * @throws Exception
     */
    public function timeout($timeout) {
        if (!is_numeric($timeout)) {
            throw new Exception(__METHOD__ . ' Failed. Numeric value expected.');
        }
        $this->timeout = $timeout;
    }

    /**
     * Set the number of concurrent workers in the pool. No limit is enforced, but processes are expensive and you should use
     * the minimum number of workers necessary. Too few workers will result in high latency situations and bigger risk
     * that if your application needs to be restarted you'll lose buffered calls.
     *
     * In `lazy` forking strategy, the processes are forked one-by-one, as needed. This is avoided when your loop_interval
     * is very short (we don't want to be forking processes if you need to loop every half second, for example) but it's
     * the most ideal setting. Read more about the forking strategy for more information.
     *
     * Part of the Daemon API - Use from your daemon to set the number of concurrent asynchronous worker processes.
     *
     * @param int $workers
     *
     * @throws Exception
     *
     * @return void
     */
    public function workers($workers) {
        if (!ctype_digit((string) $workers)) {
            throw new Exception(__METHOD__ . ' Failed. Numeric value expected.');
        }
        $this->workers = (int) $workers;
    }

    /**
     * Enable or disable worker auto restart mechanism. To find out how it works
     * look at the beginning of Core_Worker_Mediator::start() method.
     *
     * Auto restart is enabled by default
     *
     * @param bool $bool restart or not
     *
     * @throws Exception
     *
     * @return void
     */
    public function setAutoRestart($bool = true) {
        if (!is_bool($bool)) {
            throw new Exception(__METHOD__ . ' Failed. Boolean value expected.');
        }
        $this->autoRestart = $bool;
    }

    /**
     * Does the worker have at least one idle process?
     *
     * Part of the Daemon API - Use from your daemon to determine if any of your daemon's worker processes are idle
     *
     * @example Use this to implement a pattern where there is always a background worker working. Suppose your daemon writes results to a file
     *          that you want to upload to S3 continuously. You could create a worker to do the upload and set ->workers(1). In your execute() method
     *          if the worker is idle, call the upload() method. This way it should, at all times, be uploading the latest results.
     *
     * @return bool
     */
    public function isIdle() {
        return $this->workers > count($this->runningCalls);
    }
}
