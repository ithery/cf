# Module - Queue

The `CQueue` module allows you to defer time-intensive tasks to a background queue. This keeps web requests fast and improves user experience.

---

### Creating a Task

Create a task class that implements `CQueue_ShouldQueueInterface`:

```php
<?php
class MyApp_TaskQueue_ProcessImport extends CQueue_AbstractTask {
    protected $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }

    public function handle() {
        // Process the import file
    }
}
```

Or implement the interface manually with traits:

```php
<?php
class MyApp_TaskQueue_SendEmail implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_InteractsWithQueue;
    use CQueue_Trait_SerializesModels;

    public function execute() {
        // Send email
    }
}
```

---

### Dispatching Jobs

#### Queued (background)

```php
MyApp_TaskQueue_ProcessImport::dispatch($filePath);
```

#### Synchronous (immediate)

```php
MyApp_TaskQueue_ProcessImport::dispatchNow($filePath);
```

#### Using the helper

```php
c::dispatch(new MyApp_TaskQueue_ProcessImport($filePath));
```

---

### Queue Batch

Run multiple jobs as a batch with callbacks for success, failure, and completion:

```php
<?php
class MyApp_TaskQueue_BatchJob extends CQueue_AbstractTask {
    use CQueue_Trait_BatchableTrait;

    public function execute() {
        if ($this->batch()->cancelled()) {
            return;
        }
        // do work
    }
}
```

Dispatch a batch:

```php
$batch = CQueue::dispatcher()->batch([
    new MyApp_TaskQueue_BatchJob(),
    new MyApp_TaskQueue_BatchJob(),
    new MyApp_TaskQueue_BatchJob(),
])->then(function (CQueue_Batch $batch) {
    // All jobs completed successfully
})->catch(function (CQueue_Batch $batch, Throwable $e) {
    // First batch job failure detected
})->finally(function (CQueue_Batch $batch) {
    // The batch has finished executing
})->name('ImportBatch')->dispatch();
```

---

### Inspecting Batches

```php
$batch->id;              // UUID of the batch
$batch->name;            // Name of the batch
$batch->totalJobs;       // Total jobs in the batch
$batch->pendingJobs;     // Jobs not yet processed
$batch->failedJobs;      // Jobs that failed
$batch->processedJobs(); // Jobs processed so far
$batch->progress();      // Completion percentage (0-100)
$batch->finished();      // Whether the batch is complete
$batch->cancel();        // Cancel the batch
$batch->cancelled();     // Whether the batch was cancelled
```

---

### Running the Queue Worker

```bash
php cf queue:run
```

Or configure a daemon to process queued jobs continuously. See [Daemon](/docs/module/daemon).
