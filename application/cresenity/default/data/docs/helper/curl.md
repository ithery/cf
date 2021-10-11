# Helper curl


### curl::base

Fungsi abort throws CHTTP_Exception yang akan dirender oleh exception handler

```php
curl::base(403);
```

Kita dapat juga mem-provide exception's message dan custom HTTP response headers yang akan dikirim ke browser:

```php
c::abort(403, 'Unauthorized.', $headers);
```
