# Application - Element
### Standard Element

Terdapat default bawaan element yang sudah ada element.
```php
$app = c::app();
$app->addDiv(); //<div> element
$app->addA(); //<a> element
$app->addUl(); //<ul> element
$app->addOl(); //<ol> element
$app->addLi(); //<li> element
return $app;
```

untuk setiap element yang di add pada CApp, jika id tidak dipassing pada saat menambahkan element maka CApp akan melakukan generateId baru terhadap element tersebut

```php
$app->addDiv();
//<div id="00000000262e89430000000042533b79"></div>

```

tambah A
```php
$app->addA();
//<div id="00000000262e89430000000042533b79"></div>

```
