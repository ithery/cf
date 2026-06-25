# Helper curl

The `curl` helper class provides URL utility methods.

### curl::base

Returns the base URL of the application:

```php
$baseUrl = curl::base();
// 'http://myapp.dev.cresenity.com/'
```

### curl::current

Returns the current URI path:

```php
$current = curl::current();
// 'admin/user/edit'
```

With query string:

```php
$current = curl::current(true);
// 'admin/user/edit?page=2'
```

### curl::redirect

Redirects to the given URL:

```php
curl::redirect('https://example.com');
curl::redirect(curl::base() . 'home');
```

### curl::site

Returns the full site URL for a given path:

```php
$url = curl::site('admin/dashboard');
```

### curl::query

Returns the current query string:

```php
$query = curl::query();
// 'page=2&sort=name'
```
