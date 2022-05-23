# Application - Element
### Introduction

Object CApp langsung dapat ditambahkan element untuk pembuatan UI (User Interface)

Contoh element yang ada di CApp:

```php
$app = c::app();
$app->addDiv(); //<div> element
$app->addA(); //<a> element
$app->addUl(); //<ul> element
$app->addOl(); //<ol> element
$app->addLi(); //<li> element
$app->addView(); //View/ Blade View element
return $app;
```

untuk setiap element yang di add pada CApp, jika id tidak dipassing pada saat menambahkan element maka CApp akan melakukan generateId baru terhadap element tersebut

```php
$app->addDiv();
//<div id="00000000262e89430000000042533b79"></div>

```
