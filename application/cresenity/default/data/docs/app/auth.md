# Application - Authentication

CApp provides a built-in authentication system with guards, providers, and session management. The API is similar to Laravel's authentication.

### Configuration

Authentication is configured in `config/auth.php`. The two key concepts are:

- **Guards** — define how users are authenticated for each request (e.g. session-based, token-based)
- **Providers** — define how users are retrieved from the database

```php
<?php
// system/config/auth.php (defaults)
return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],
];
```

### Requiring Login

Use `setLoginRequired()` to protect pages that require authentication:

```php
<?php
// In bootstrap.php (applies to all pages)
c::app()->setLoginRequired(true);

// In a specific controller
public function index() {
    $app = c::app();
    $app->setLoginRequired(true);
    // ...
}
```

When login is required and the user is not authenticated, CApp automatically renders the login view.

### Database Setup

The framework includes a default `CApp_Model_User` model for authentication. When creating a users table, ensure it has:

- A `password` column — minimum 60 characters (for bcrypt hashes)
- A `remember_token` column — minimum 100 characters (for "remember me" functionality)

### Checking Authentication

```php
// Check if user is logged in
if (c::auth()->check()) {
    // authenticated
}

// Check if user is a guest
if (c::auth()->guest()) {
    // not authenticated
}

// Get the authenticated user
$user = c::auth()->user();

// Get the authenticated user's ID
$id = c::auth()->id();
```

The `c::app()->user()` shorthand also returns the current user:

```php
$user = c::app()->user();
```

### Login

Authenticate a user with credentials using `attempt()`:

```php
<?php
public function login() {
    $request = c::request();

    if ($request->isMethod('post')) {
        $email = $request->input('email', '');
        $password = $request->input('password', '');
        $rememberMe = $request->boolean('remember-me');

        try {
            $success = c::app()->auth()->attempt(
                ['username' => $email, 'password' => $password],
                $rememberMe
            );

            if ($success) {
                return c::redirect('app/home');
            }

            return MyApp::jsonResponse(1, 'Invalid credentials');
        } catch (Exception $e) {
            return MyApp::jsonResponse(1, $e->getMessage());
        }
    }

    // Already logged in
    if (c::auth()->check()) {
        return c::redirect('app/home');
    }

    // Show login page
    return c::app();
}
```

### Logout

```php
<?php
public function logout() {
    c::auth()->logout();

    return c::redirect('home');
}
```

### Guards

Use a specific guard when your application has multiple authentication contexts (e.g. web users and API tokens):

```php
// Use the default guard
$user = c::auth()->user();

// Use a specific guard
$user = c::auth('api')->user();

// Via CApp
$user = c::app()->auth('admin')->user();
```

### Auth Features

Configure which authentication features are available in `config/app.php`:

```php
<?php
return [
    'auth' => [
        'features' => [
            'login' => true,
            'registration' => true,
            'resetPasswords' => true,
            'emailVerification' => false,
        ],
    ],
];
```
