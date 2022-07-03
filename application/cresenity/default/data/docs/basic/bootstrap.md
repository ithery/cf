# Bootstrap

Bootstraping atau setup default untuk keseluruhan behaviour yang ada project dapat diletakkan pada `bootstrap.php`


### Set Locale At Runtime

```php
CF::setLocale('id_ID');
```

### robots.txt (>=1.3)

Dibawah ini adalah contoh untuk memblock url dengan /admin pada production dan semua url pada development

```php
c::router()->get('robots.txt', function () {
    return CHTTP::robotsTxt()->addUserAgent('*')->addDisallow(CF::isProduction() ? '/admin' : '/')->toResponse();
});
```
