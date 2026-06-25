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

        $this->loopCount++;
        if ($this->loopCount > 10000) {
            // Auto-restart to prevent memory leaks
            $this->restart();
        }
    }
}
```

---

### Registering a Daemon

Register the daemon in `bootstrap.php`:

```php
c::manager()->registerDaemon(MyApp_Daemon_QueueRunner::class);
```

---

### Configuration

| Property | Description |
|----------|------------|
| `$loopInterval` | Seconds between each `execute()` call |
| `$loopCount` | Current loop iteration count |

| Method | Description |
|--------|------------|
| `setup()` | Called once when the daemon starts |
| `execute()` | Called on each loop iteration |
| `restart()` | Restart the daemon process |
| `log($message)` | Write to daemon log file |
| `debug($message)` | Write debug message (non-production only) |
| `error($message)` | Write error message |
| `getConfig($key)` | Get daemon configuration value |

---

### Running Daemons

```bash
php cf daemon:run
```

---

### Daemon UI Manager

Monitor registered daemons with a built-in UI:

```php
<?php
class Controller_Admin_Daemon extends CController {
    use CTrait_Controller_Application_Manager_Daemon;

    protected function getTitle() {
        return 'Daemon Manager';
    }
}
```

This provides a UI to start, stop, and monitor daemon services.
