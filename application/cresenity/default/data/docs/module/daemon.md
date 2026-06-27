# Module - Daemon

The `CDaemon` module provides long-running background services that run continuously on the server. Daemons are ideal for queue workers, WebSocket servers, and real-time data processors.

---

### Creating a Daemon Service

Extend `CDaemon_ServiceAbstract` and implement `setup()` and `execute()`:

```php
<?php
class MyApp_Daemon_QueueRunner extends CDaemon_ServiceAbstract {
    protected $loopInterval = 1;

    public function setup() {
        // Run once on startup
        c::db()->disableBenchmark();
    }

    public function execute() {
        // Run on each loop iteration
        CQueue::run('database', [
            'sleep' => 0,
        ]);
    }
}
```

`setup()` runs once when the daemon starts. `execute()` is called repeatedly at the `$loopInterval` frequency.

---

### Registering a Daemon

Register the daemon in `bootstrap.php` so the framework and admin UI know about it:

```php
CManager::registerDaemon(MyApp_Daemon_QueueRunner::class);
```

With a group name (for organizing multiple daemons in the UI):

```php
CManager::registerDaemon(MyApp_Daemon_QueueRunner::class, null, 'Queue');
CManager::registerDaemon(MyApp_Daemon_WebSocket::class, null, 'WebSocket');
```

---

### Service Properties

| Property | Default | Description |
|---|---|---|
| `$loopInterval` | `null` | Seconds between each `execute()` call. Use decimals for sub-second intervals (e.g. `0.5` = 2x/sec). `null` or `0` = no sleep between calls. |
| `$autoRestartInterval` | `43200` | Auto-restart interval in seconds. `43200` = every 12 hours. Prevents memory leaks in long-running processes. |
| `$idleProbability` | `0.50` | Probability (0.0–1.0) of firing `ON_IDLE` event when not using `$loopInterval`. Ignored when `$loopInterval` is set. |
| `$terminateLimit` | `20` | Max seconds to wait for graceful shutdown before force-killing. |
| `$logSizeToRotate` | `512000` | Log file size in bytes before rotation (500 KB). |
| `$logKeepToRotate` | `10` | Number of rotated log files to keep. |
| `$isDaemonContinueOnFatalError` | `false` | If `true`, daemon continues running after a fatal error instead of shutting down. |
| `$debug` | `true` | Enable debug logging. |
| `$stdout` | `false` | Output logs to stdout (useful for development). |

---

### Service Methods

| Method | Description |
|---|---|
| `setup()` | Called once when the daemon starts. Initialize resources here. |
| `execute()` | Called on each loop iteration. Your main work goes here. |
| `restart($rotateLog)` | Restart the daemon process. Pass `true` to rotate log files before restart. |
| `stop($exit)` | Stop the daemon gracefully. |
| `log($message)` | Write to the daemon log file. |
| `debug($message)` | Write debug message (only when `$debug` is true). |
| `error($message)` | Write error message. |
| `fatalError($message)` | Write fatal error and shut down the daemon. |
| `getConfig($key)` | Get daemon configuration value. |
| `runtime()` | Returns seconds since daemon started. |
| `pid()` | Get/set the current process ID. |

---

### Event Hooks

Use `on()` to listen for lifecycle events:

```php
public function setup() {
    $this->on(self::ON_IDLE, function() {
        // Do housekeeping when idle
    });

    $this->on(self::ON_SIGNAL, function($signal) {
        $this->log('Received signal: ' . $signal);
    });
}
```

| Constant | Event |
|---|---|
| `ON_ERROR` | `error()` or `fatalError()` was called |
| `ON_SIGNAL` | The daemon received an OS signal |
| `ON_INIT` | Library initialization complete, before `setup()` |
| `ON_PREEXECUTE` | Inside event loop, right before `execute()` |
| `ON_POSTEXECUTE` | Right after `execute()` |
| `ON_FORK` | In a background process right after fork |
| `ON_PIDCHANGE` | Whenever the PID changes |
| `ON_IDLE` | Called when there is idle time at the end of a loop interval |
| `ON_REAP` | Notification that a child process has exited |
| `ON_SHUTDOWN` | Called at the top of the destructor |

---

### Running Daemons

From the command line:

```bash
# Run the daemon (foreground)
phpcf daemon:run MyApp_Daemon_QueueRunner

# Start the daemon (background)
phpcf daemon:start MyApp_Daemon_QueueRunner

# Stop the daemon
phpcf daemon:stop MyApp_Daemon_QueueRunner

# Check status
phpcf daemon:status MyApp_Daemon_QueueRunner
```

---

### CDaemon_Runner

Use `CDaemon::createRunner()` to manage daemons programmatically:

```php
$runner = CDaemon::createRunner(MyApp_Daemon_QueueRunner::class);

// Check status
$runner->isRunning();     // bool
$runner->status();        // status string
$runner->getPid();        // process ID or null
$runner->getStartTime();  // start time

// Control
$runner->start();         // start in background
$runner->stop();          // graceful stop
$runner->stop(true);      // force stop

// Logging
$runner->getLogFile();    // log file path
$runner->getLog();        // read log contents
$runner->rotateLog();     // rotate the log file
$runner->logDump();       // dump recent log entries
```

---

### Auto-Restart with Cron

Keep daemons running by creating a cron task that checks and restarts stopped services:

```php
<?php
class MyTaskQueue_Server_RestartPrimaryService {
    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_InteractsWithQueue;
    use CQueue_Trait_SerializesModels;

    public function execute() {
        $this->restartServiceWhenNotRunning(
            CDaemon::createRunner(MyApp_Daemon_QueueRunner::class)
        );
    }

    private function restartServiceWhenNotRunning(CDaemon_Runner $service) {
        if (!$service->isRunning()) {
            $service->rotateLog();
            $service->start();
            CCron::log('successfully restart ' . $service->getServiceClass());
        }
    }
}
```

Register via a cron class that runs every 5 minutes:

```php
<?php
class MyCron_Server_RestartPrimaryService extends MyCron_Cron {
    protected $schedule = '*/5 * * * *';

    public function handler() {
        MyTaskQueue_Server_RestartPrimaryService::dispatchNow();
    }
}
```

---

### Admin Daemon Manager

Add a daemon management page to your admin panel:

```php
<?php
class Controller_Admin_Sysadmin_Daemon extends MyAdmin_Controller {
    use CTrait_Controller_Application_Manager_Daemon;

    protected function getTitle() {
        return 'Daemon Manager';
    }
}
```

This provides a UI to:
- View all registered daemons grouped by category
- Start/Stop individual services
- View real-time log output
- Check running status and PID

---

### Memory Management

Long-running PHP processes can accumulate memory. Best practices:

```php
public function execute() {
    // Periodic auto-restart (built-in via $autoRestartInterval)
    // Default: every 12 hours

    // Manual restart after N iterations
    $this->loopCount++;
    if ($this->loopCount > 10000) {
        $this->restart();
    }

    // Disable query log to prevent memory growth
    // Call in setup():
    c::db()->disableBenchmark();
}
```

---

### WebSocket Daemon

For WebSocket servers, extend `CWebSocket_Daemon_WebSocketService` instead of `CDaemon_ServiceAbstract`. See the [CWebSocket](/docs/other/websocket) documentation for details.
