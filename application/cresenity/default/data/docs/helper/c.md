# Helper c

The `c` helper class provides convenient static methods for common framework operations.

### c::app

Returns the `CApp` singleton instance:

```php
$app = c::app();
```

### c::request

Returns the current `CHTTP_Request` instance, or a specific input value:

```php
$request = c::request();
$name = c::request('name');
$name = c::request('name', 'default');
$data = c::request(['name', 'email']);
```

### c::config

Returns a configuration value using dot notation:

```php
$appName = c::config('app.title');
$debug = c::config('core.debug', false);
```

### c::env

Returns an environment variable from `env.php`:

```php
$dbHost = c::env('MYSQL_HOST', '127.0.0.1');
```

### c::auth

Returns the authentication guard:

```php
$user = c::auth()->user();
if (c::auth()->check()) { }
```

### c::view

Returns a view instance:

```php
return c::view('dashboard', ['title' => 'Home']);
```

### c::response

Returns a response instance. With no arguments, returns the response factory:

```php
return c::response('OK');
return c::response()->json(['status' => 'ok']);
return c::response()->view('error', [], 500);
```

### c::redirect

Returns a redirect response:

```php
return c::redirect('home');
return c::redirect()->back();
```

### c::url

Generates a URL for the given path:

```php
$url = c::url('admin/user/edit/' . $id);
```

### c::abort

Throws an HTTP exception that will be rendered by the exception handler:

```php
c::abort(404);
c::abort(403, 'Unauthorized.', $headers);
```

### c::abortIf

Throws an HTTP exception if the given condition is `true`:

```php
c::abortIf(!$user->isAdmin(), 403);
```

### c::abortUnless

Throws an HTTP exception if the given condition is `false`:

```php
c::abortUnless($user->isAdmin(), 403);
```

### c::collect

Creates a new `CCollection` instance:

```php
$collection = c::collect([1, 2, 3]);
```

### c::e

Runs `htmlspecialchars` with double encoding enabled:

```php
echo c::e('<html>foo</html>');
// &lt;html&gt;foo&lt;/html&gt;
```

### c::json

Encodes a value as JSON, suitable for embedding in HTML attributes:

```php
$json = c::json(['key' => 'value']);
```

### c::media

Returns the URL for a media asset:

```php
$url = c::media('img/logo.png');
```

### c::str

Returns a `CBase_Stringable` instance for fluent string manipulation:

```php
$string = c::str('Cresenity')->append(' Framework');
// 'Cresenity Framework'
```

Without arguments, returns the `cstr` class:

```php
$snake = c::str()->snake('FooBar');
// 'foo_bar'
```

### c::trans / c::__

Translates the given key using localization files. `c::__` is an alias of `c::trans`:

```php
echo c::__('Welcome');
echo c::__('Hello, :name', ['name' => 'John']);
echo c::trans('messages.welcome');
```

### c::transChoice

Translates with pluralization:

```php
echo c::transChoice('messages.notifications', $count);
```

### c::pregReplaceArray

Replaces a pattern sequentially using an array:

```php
$string = 'Between :start and :end';
$result = c::pregReplaceArray('/:[a-z_]+/', ['8:30', '9:00'], $string);
// 'Between 8:30 and 9:00'
```

### c::dispatch

Dispatches a job to the queue:

```php
c::dispatch(new MyJob($data));
```

### c::now

Returns the current datetime as a Carbon instance:

```php
$now = c::now();
$formatted = c::now()->format('Y-m-d');
```

### c::optional

Allows accessing properties on an object that may be null:

```php
$name = c::optional($user)->name;
// Returns null instead of error if $user is null
```

### c::validator

Creates a validator instance:

```php
$validated = c::validator()->validate($data, [
    'name' => 'required',
    'email' => 'required|email',
]);
```

### c::db

Returns the database connection:

```php
$db = c::db();
$results = c::db()->query('SELECT * FROM users');
```

### c::div

Creates a new `CElement_Element_Div` instance:

```php
$div = c::div();
$div->add('Content');
```
