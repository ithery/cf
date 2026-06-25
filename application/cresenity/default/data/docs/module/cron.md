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

Add this to your system crontab:

```bash
* * * * * cd /path/to/project && php cf cron:run >> /dev/null 2>&1
```

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
