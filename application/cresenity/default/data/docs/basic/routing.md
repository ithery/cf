# Routing

### Default Routing

Routing otomatis berdasarkan uri yang tersedia, semisal uri yang diakses adalah `/example`
maka yang akan otomatis mengakses file contollers example dengan method index sebagai default

Jika yang uri diakses adalah `/example/home` maka code akan berjalan di controller example dengan method home

```php
class Controller_Example extends CController {
    public function index() {
        return c::response('index');
    }

    public function home() {
        return c::response('home');
    }
}
```
