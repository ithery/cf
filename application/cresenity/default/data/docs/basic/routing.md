# Routing

### Default Routing

Routing akan berjalan otomatis berdasarkan uri yang tersedia, semisal uri yang diakses adalah `/example`
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

### Pass parameter to controller method

Untuk melakukan pass parameter pada fungsi di controller, bergantung pada url yang terakses.
semisal url yang diakses adalah `/example/user/james`

```php
class Controller_Example extends CController {
    public function user($name=null) {
        //$name value is james, when url is `/example/user/james`
        //$name value is null when url is `/example/user`
        return c::response($name);
    }


}
```
