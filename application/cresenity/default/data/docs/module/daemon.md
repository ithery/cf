# CDaemon

### Introduction

Saat membangun aplikasi web, Anda mungkin perlu memiliki beberapa service yang harus berjalan terus menerus dibackground pada server.



Contoh Kode membuat class daemon:
```php
class MYDaemon_QueueRunnerDaemon extends CDaemon_ServiceAbstract {
    protected $loopInterval = 1; // set loop interval to 1 seconds

    /**
     * Run once time when daemon startup
     */
    public function setup() {
        // make sure to disable benchmark to optimize memory when daemon running with query
        c::db()->disableBenchmark();
    }

    /**
     * Run each time loop interval
     */
    public function execute() {
        //in this example we will run queue
        CQueue::run('database', [
            'sleep' => 0,
        ]);
        $this->loopCount++;
        if ($this->loopCount > 10000) {
            //automatically restart daemon for prevent memory leak
            $this->restart();
        }
    }
}

```

contoh kode untuk registerkan daemon ke framework:
```php
c::manager()->registerDaemon(MYDaemon_QueueRunnerDaemon::class);

```

### UI Previewer

Setelah diregister ke framework mennggunakan CManager, maka daemon dapat dipantau melalui ui.

Contoh Kode untuk ui previewer:
```php
<?php
class Controller_Daemon extends CController {
    use CTrait_Controller_Application_Manager_Daemon;

    protected function getTitle() {
        return 'Daemon';
    }
}

```
