# Controller

Controller default harus mengextends dari CController

```php
class Controller_Example extends CController {
    public function index() {
        return c::response('index');
    }
}
```

### Controller Method Access

Akses controller akan selalu terbuka jika method adalah `public`, gunakan method `private` atau `protected` jika ingin method controller tidak dapat diakses secara url


```php
class Controller_Example extends CController {
    public function index() {
        return $this->home();
    }

    protected function home() {
        return c::response('home');
    }
}
```
