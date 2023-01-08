# Base Class

Base class secara default mengambil class `CApp_Base` yang terletak pada `system\CApp\Base.php`

Base class dapat dioverride pada config `app.php` dengan key `classes.base`

Example config:
```php
    // ...

    'classes' => [
        'base' => CApp_Base::class,
    ],

    // ...
```
