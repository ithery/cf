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

### &lt;h1&gt;
```php
$h1 = $app->addH1();
$h1->add('Header H1')
```

### &lt;h2&gt;
```php
$h2 = $app->addH2();
$h2->add('Header H2')
```

### &lt;h3&gt;
```php
$h3 = $app->addH3();
$h3->add('Header H3')
```

### &lt;h4&gt;
```php
$h4 = $app->addH4();
$h4->add('Header H4')
```


### &lt;h5&gt;
```php
$h5 = $app->addH5();
$h5->add('Header H5')
```


### &lt;h6&gt;
```php
$h6 = $app->addH6();
$h6->add('Header H6')
```
