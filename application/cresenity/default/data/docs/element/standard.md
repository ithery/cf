# Element - Standard Element
### Introduction

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


### &lt;div&gt;
```php
$div = $app->addDiv();
$div->add('Ini didalam div');
```

### &lt;a&gt;
```php
$a = $app->addA();
$a->setHref(c::url('to/url'));
$a->setTarget('_blank');
$a->add('Click link ini');
$a->add('Click link ini');
```
