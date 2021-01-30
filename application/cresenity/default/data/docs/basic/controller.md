# Controller

Controller default harus mengextends dari CController

```php
class Controller_Example extends CController {
    public function index() {
        return c::response('index');
    }
}
```
