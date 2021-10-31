# Helper c

### c::abort

Fungsi abort throws CHTTP_Exception yang akan dirender oleh exception handler

```php
c::abort(403);
```

Kita dapat juga mem-provide exception's message dan custom HTTP response headers yang akan dikirim ke browser:

```php
c::abort(403, 'Unauthorized.', $headers);
```

### c::abortIf()

fungsi abortIf throws HTTP exception jika parameter boolean expression bernilai `true`:

```php
c::abortIf(! Auth::havePermission('admin'), 403);
```

Seperti fungsi abort, Kita dapat juga mem-provide exception's message pada parameter ketiga dan custom HTTP response headers pada parameter keempat.

### c::abortUnless()

fungsi abortUnless throws HTTP exception jika parameter boolean expression bernilai `false`:

```php
c::abortUnless(Auth::havePermission('admin'), 403);
```

Seperti fungsi abort, Kita dapat juga mem-provide exception's message pada parameter ketiga dan custom HTTP response headers pada parameter keempat.
