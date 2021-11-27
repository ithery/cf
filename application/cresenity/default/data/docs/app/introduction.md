# Application - Introduction
### Prolog

Salah satu kelebihan CF adalah mempunyai object CApp yang dapat digunakan sebagai inti dari sebuah pembuatan halaman.
Instance CApp bersifat singleton dan dapat didapat dari fungsi `c::app`

```php
class Controller_Example extends CController {
    public function index() {
        $app = c::app();

        return $app;
    }

}
```
