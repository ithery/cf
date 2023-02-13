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
