# Module - Cron

The `CCron` module lets you schedule recurring tasks. Tasks are registered in `bootstrap.php` and executed by the system cron runner.

---

### Registering Jobs

#### Closure Callback

```php
c::cron()->call(function () {
    // do something
})->cron('0 1 * * *')->name('NightlyCleanup');
```

#### Job Class

```php
c::cron()->job(MyApp_TaskQueue_DailyReport::class)
    ->cron('0 8 * * *')
    ->name('DailyReport');
```

#### Command

```php
c::cron()->command('cache:clear')
    ->cron('0 0 * * *')
    ->name('ClearCache');
```

---

### Cron Expression

The `cron()` method accepts a standard 5-field cron expression:

```
* * * * *
│ │ │ │ └── Day of week (0-7, Sun=0 or 7)
│ │ │ └──── Month (1-12)
│ │ └────── Day of month (1-31)
│ └──────── Hour (0-23)
└────────── Minute (0-59)
```

| Expression | Schedule |
|-----------|----------|
| `* * * * *` | Every minute |
| `0 * * * *` | Every hour |
| `0 0 * * *` | Daily at midnight |
| `0 8 * * *` | Daily at 8 AM |
| `0 0 * * 0` | Every Sunday at midnight |
| `*/5 * * * *` | Every 5 minutes |

---

### Running the Scheduler

CF's cron system does **not** run automatically. You must register a system-level crontab entry on the server for each application domain. Without this, none of the registered cron jobs will execute.

#### Adding the Crontab Entry

SSH into your server and edit the crontab:

```bash
crontab -e
```

Add a line for your application domain:

```bash
* * * * * /usr/local/lsws/lsphp84/bin/php "/home/sandbox/public_html/index.php" "cresenity/cron" "myapp.dev.cresenity.com" 1> "/dev/null" 2>&1 &
```

The format is:

```
* * * * * {php_binary} "{path_to_index.php}" "cresenity/cron" "{domain}" 1> "/dev/null" 2>&1 &
```

| Part | Description |
|---|---|
| `* * * * *` | Run every minute (the framework handles individual job scheduling) |
| `{php_binary}` | Path to PHP CLI binary (e.g. `/usr/local/lsws/lsphp84/bin/php` or `/usr/bin/php`) |
| `{path_to_index.php}` | Full path to the CF `index.php` entry point |
| `"cresenity/cron"` | The internal route that triggers the cron runner |
| `{domain}` | The application domain — CF uses this to determine which app config and bootstrap to load |

#### Multiple Applications

Each application needs its own crontab entry:

```bash
* * * * * /usr/bin/php "/home/sandbox/public_html/index.php" "cresenity/cron" "myapp.example.com" 1> "/dev/null" 2>&1 &
* * * * * /usr/bin/php "/home/sandbox/public_html/index.php" "cresenity/cron" "otherapp.example.com" 1> "/dev/null" 2>&1 &
```

#### Verifying

Check that the crontab is registered:

```bash
crontab -l | grep myapp
```

You can verify cron execution from the admin Cron Manager UI, which shows the last run time for each registered job.

---

### Cron UI Manager

Add a cron management page using the built-in controller trait:

```php
<?php
class Controller_Admin_Cron extends CController {
    use CTrait_Controller_Application_Manager_Cron;

    protected function getTitle() {
        return 'Cron Manager';
    }
}
```

This provides a UI to view registered cron jobs, their schedule, last run time, and status.
