# CDaemon

### Introduction

Saat membangun aplikasi web, Anda mungkin perlu memiliki beberapa service yang harus berjalan terus menerus dibackground pada server.




### UI Previewer


Contoh Kode:
```php
<?php
class Controller_Daemon extends CController {
    use CTrait_Controller_Application_Manager_Daemon;

    protected function getTitle() {
        return 'Daemon';
    }
}

```
