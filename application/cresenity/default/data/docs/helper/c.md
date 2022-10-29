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

### c::e

The `c::e` function runs PHP's `htmlspecialchars` function with the `double_encode` option set to `true` by default:
```php
    echo c::e('<html>foo</html>');

    // &lt;html&gt;foo&lt;/html&gt;
```

### c::pregReplaceArray
The `c::pregReplaceArray` function replaces a given pattern in the string sequentially using an array:
```php
    $string = 'The event will take place between :start and :end';

    $replaced = c::pregReplaceArray('/:[a-z_]+/', ['8:30', '9:00'], $string);

    // The event will take place between 8:30 and 9:00
```

### c::str

The `c::str` function returns a new `CBase_Stringable` instance of the given string. This function is equivalent to the `cstr::of` method:
```php
    $string = c::str('Cresenity')->append(' Framework');

    // 'Cresenity Framework'
```
If no argument is provided to the `c::str` function, the function returns an instance of `cstr`:
```php
    $snake = c::str()->snake('FooBar');

    // 'foo_bar'
```

### `c::trans()`

The `c::trans` function translates the given translation key using your localization files:
```php
    echo c::trans('messages.welcome');
```
If the specified translation key does not exist, the `c::trans` function will return the given key. So, using the example above, the `c::trans` function would return `messages.welcome` if the translation key does not exist.

### c::__

The `c::__` is an alias of `c::trans`

### c::transChoice

The `c::transChoice` function translates the given translation key with inflection:
```php
    echo trans_choice('messages.notifications', $unreadCount);
```
If the specified translation key does not exist, the `c::transChoice` function will return the given key. So, using the example above, the `c::transChoice` function would return `messages.notifications` if the translation key does not exist.
