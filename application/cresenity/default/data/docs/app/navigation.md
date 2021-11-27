# Application - Navigation
### Creating The Navigation

Untuk membuat file navigasi diletakkan pada folder navs. default file yang diload adalah `nav.php` pada folder navs.


```php
return [
    [
        'name' => 'dashboard',
        'label' => c::__('Dashboard'),
        'controller' => 'home',
        'method' => 'index',
        'icon' => 'home',
    ],
];
```
