# CQueue

### Introduction

Saat membangun aplikasi web, Anda mungkin memiliki beberapa tugas, seperti mengurai dan menyimpan file CSV yang diunggah, yang membutuhkan waktu terlalu lama untuk dijalankan selama permintaan web biasa. Untungnya, CF memungkinkan Anda dengan mudah membuat pekerjaan queue yang dapat diproses di background. Dengan memindahkan tugas yang membutuhkan banyak waktu ke queue, aplikasi Anda dapat merespons permintaan web dengan sangat cepat dan memberikan user experience yang lebih baik kepada user Anda.



Contoh Kode Membuat Task Queue:
```php

class MYTaskQueue_SomeQueue implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_InteractsWithQueue;
    use CQueue_Trait_SerializesModels;

    public function execute() {
        //do something here
    }
}


```

Cara running job dengan queue:

```php
MYTaskQueue_SomeQueue::dispatch();
```

Cara running job tanpa queue :

```php
MYTaskQueue_SomeQueue::dispatchNow();
```

### Queue Batch

Untuk menentukan Task Queue yang dapat digabungkan, Anda harus membuat tugas seperti biasa. namun, Anda harus menambahkan trait CQueue_Trait_BatchableTrait ke kelas Task Queue anda. Trait ini memberikan akses ke metode batch yang dapat digunakan untuk mengambil batch saat ini yang dijalankan oleh pekerjaan di dalam:


Contoh Kode Membuat Task Queue Batch:
```php

class MYTaskQueue_SomeBatchQueue implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_BatchableTrait;
    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_InteractsWithQueue;
    use CQueue_Trait_SerializesModels;

    public function execute() {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...

            return;
        }
        //do something here
    }
}


```

Cara running job dengan queue batch:

```php
$batch = CQueue::dispatcher()->batch([
    new MYTaskQueue_SomeBatchQueue(),
    new MYTaskQueue_SomeBatchQueue(),
    new MYTaskQueue_SomeBatchQueue(),
])->then(function (CQueue_Batch $batch) {
    // All jobs completed successfully...
})->catch(function (CQueue_Batch $batch, Throwable $e) {
    // First batch job failure detected...
})->finally(function (CQueue_Batch $batch) {
    // The batch has finished executing...
})->dispatch();

return $batch->id
```

Memberi nama pada batch


```php
CQueue::dispatcher()->batch([
    new MYTaskQueue_SomeBatchQueue(),
    new MYTaskQueue_SomeBatchQueue(),
    new MYTaskQueue_SomeBatchQueue(),
])->name('Broadcasting with ID: '. $broadcastId)->dispatch();
```

### Inspecting Batches

Instance CQueue_Batch yang disediakan untuk callback batch memiliki berbagai properti dan metode untuk membantu Anda dalam berinteraksi dengan dan memeriksa batch Task Queue tertentu:

```php
// The UUID of the batch...
$batch->id;

// The name of the batch (if applicable)...
$batch->name;

// The number of jobs assigned to the batch...
$batch->totalJobs;

// The number of jobs that have not been processed by the queue...
$batch->pendingJobs;

// The number of jobs that have failed...
$batch->failedJobs;

// The number of jobs that have been processed thus far...
$batch->processedJobs();

// The completion percentage of the batch (0-100)...
$batch->progress();

// Indicates if the batch has finished executing...
$batch->finished();

// Cancel the execution of the batch...
$batch->cancel();

// Indicates if the batch has been cancelled...
$batch->cancelled();

```
