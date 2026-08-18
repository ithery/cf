# Routing

Cresenity Framework uses a **convention-based routing** system. In most cases, you do not need to define routes manually — the framework automatically maps URLs to controller classes and methods based on the URI structure. For special cases, you can register explicit routes that take priority over the auto-discovery.

---

### Auto-Discovery

The framework automatically resolves a URL to a controller file and method. No route registration is needed.

#### Basic Example

Given the following controller at `controllers/example.php`:

```php
class Controller_Example extends CController {
    public function index() {
        return c::response('index');
    }

    public function home() {
        return c::response('home');
    }
}
```

| URL | Controller | Method |
|-----|-----------|--------|
| `/example` | `Controller_Example` | `index` (default) |
| `/example/home` | `Controller_Example` | `home` |

#### Nested Controllers

Controllers can be organized into subdirectories. The directory structure maps to the URL path.

Given the controller at `controllers/admin/setting.php`:

```php
class Controller_Admin_Setting extends CController {
    public function index() {
        // GET /admin/setting
    }

    public function general() {
        // GET /admin/setting/general
    }
}
```

| URL | Controller | File |
|-----|-----------|------|
| `/admin/setting` | `Controller_Admin_Setting` | `controllers/admin/setting.php` |
| `/admin/setting/general` | `Controller_Admin_Setting` | method `general()` |

#### Method Parameters

URL segments after the method name are passed as method parameters in order.

```php
class Controller_User extends CController {
    public function profile($username, $tab = 'overview') {
        // GET /user/profile/james         → $username = 'james', $tab = 'overview'
        // GET /user/profile/james/posts   → $username = 'james', $tab = 'posts'
    }
}
```

If a URL provides fewer arguments than the method's required parameters, the framework returns a `404 Not Found` response.

#### Default Method

When no method segment is present in the URL, the framework calls the `index` method. The default controller for the root URL (`/`) is configured in `config/routes.php`:

```php
<?php
return [
    '_default' => 'home',  // GET / → Controller_Home::index()
];
```

---

### HTTP Verb Prefixes

By default, auto-discovered methods accept **all HTTP verbs** (GET, POST, PUT, etc.). To restrict a method to specific verbs, prefix the method name with the HTTP verb in lowercase.

```php
class Controller_Product extends CController {
    public function index() {
        // All verbs — GET /product (backward compatible)
    }

    public function getDetail($id) {
        // GET and HEAD only — GET /product/detail/5
    }

    public function postStore() {
        // POST only — POST /product/store
    }

    public function putUpdate($id) {
        // PUT only — PUT /product/update/5
    }

    public function deleteRemove($id) {
        // DELETE only — DELETE /product/remove/5
    }

    public function anyWebhook() {
        // All verbs (explicit) — any method /product/webhook
    }
}
```

The URL is based on the segment **after** the verb prefix. For example, `postStore` maps to the URL `/product/store`, not `/product/postStore`.

| Prefix | Allowed HTTP Methods |
|--------|---------------------|
| `get` | GET, HEAD |
| `post` | POST |
| `put` | PUT |
| `patch` | PATCH |
| `delete` | DELETE |
| `options` | OPTIONS |
| `any` | All methods |
| *(no prefix)* | All methods (backward compatible) |

If a URL matches a verb-prefixed method for a **different** verb, the framework returns a `405 Method Not Allowed` response with the appropriate `Allow` header.

---

### Catch-All Methods

Controllers can implement the `__call` magic method to handle any undefined method as a catch-all. This is useful for dynamic page routing.

```php
class Controller_Docs extends CController {
    public function index() {
        return $this->page();
    }

    public function page($category = null, $page = null) {
        // Load and render documentation page
    }

    public function __call($method, $args) {
        // GET /docs/starter/installation
        // → $method = 'starter', $args = ['installation']
        return $this->page($method, carr::first($args));
    }
}
```

---

### Explicit Routes

For cases that don't fit the convention (custom URLs, closures, redirects), you can register routes explicitly. Explicit routes are checked **before** the auto-discovery fallback.

Register routes in your application's `bootstrap.php` file using `c::router()`.

#### Basic Routes

```php
c::router()->get('/about', function () {
    return c::view('about');
});

c::router()->post('/contact', function () {
    // handle form submission
});
```

#### Available Methods

```php
c::router()->get($uri, $action);
c::router()->post($uri, $action);
c::router()->put($uri, $action);
c::router()->patch($uri, $action);
c::router()->delete($uri, $action);
c::router()->options($uri, $action);
c::router()->any($uri, $action);         // all verbs
c::router()->match(['GET', 'POST'], $uri, $action);  // specific verbs
```

#### Route Parameters

Use curly braces to define route parameters:

```php
c::router()->get('/user/{id}', function ($id) {
    return c::response('User: ' . $id);
});

// Optional parameter
c::router()->get('/user/{name?}', function ($name = 'guest') {
    return c::response('Hello, ' . $name);
});
```

#### Parameter Constraints

Use the `where` method to constrain parameters with regular expressions:

```php
c::router()->get('/user/{id}', function ($id) {
    // ...
})->where('id', '[0-9]+');

// Multiple constraints
c::router()->get('/post/{id}/{slug}', function ($id, $slug) {
    // ...
})->where(['id' => '[0-9]+', 'slug' => '[a-z0-9\-]+']);
```

#### Controller Actions

Instead of closures, you can point routes to controller methods using the `Controller@method` syntax:

```php
c::router()->get('/dashboard', 'Controller_App_Dashboard@index');
c::router()->post('/api/login', 'Controller_Api_Auth@postLogin');
```

#### Named Routes

Assign a name to a route for easy URL generation:

```php
c::router()->get('/user/{id}/profile', function ($id) {
    // ...
})->name('user.profile');
```

#### Route Groups

Group routes that share attributes like a prefix or middleware:

```php
c::router()->group(['prefix' => 'api', 'middleware' => 'auth'], function ($router) {
    $router->get('/users', function () {
        // GET /api/users
    });

    $router->get('/posts', function () {
        // GET /api/posts
    });
});
```

Available group attributes:

| Attribute | Description |
|-----------|------------|
| `prefix` | URL prefix for all routes in the group |
| `middleware` | Middleware applied to all routes in the group |
| `namespace` | Controller namespace prefix |
| `domain` | Domain constraint |
| `name` / `as` | Name prefix for all routes in the group |
| `where` | Parameter constraints for all routes in the group |

#### Redirects

```php
c::router()->redirect('/old-page', '/new-page');              // 302 redirect
c::router()->permanentRedirect('/old-page', '/new-page');     // 301 redirect
```

#### Fallback Route

Register a fallback route that handles any URL not matched by other routes:

```php
c::router()->fallback(function () {
    return c::response('Page not found', 404);
});
```

---

### URI Routing (Config-Based)

For simple URL rewrites, you can define mappings in `config/routes.php`. These are evaluated before controller resolution and support regex patterns.

```php
<?php
return [
    '_default' => 'home',
    'about'    => 'page/about',
    'blog/([0-9]+)' => 'post/detail/$1',
];
```

In this example:
- `/about` is internally routed to `Controller_Page@about`
- `/blog/42` is internally routed to `Controller_Post@detail` with argument `42`

---

### Route Resolution Order

When a request comes in, the framework resolves the route in the following order:

1. **Explicit routes** — routes registered via `c::router()` are checked first
2. **URI routing config** — mappings in `config/routes.php` rewrite the URI
3. **Auto-discovery** — the URI is resolved to a controller file and method
4. **Fallback route** — if registered, handles unmatched requests
5. **404 Not Found** — if nothing matches
