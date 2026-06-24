# Base Class

The Base Class provides a set of static helper methods commonly used throughout your application — accessing the current user, organization, environment, request data, and more. Every application should define its own base class by using the `CApp_Trait_BaseTrait` trait.

---

### Default Base Class

The framework provides a default base class `CApp_Base` located at `system/libraries/CApp/Base.php`:

```php
<?php
class CApp_Base implements CApp_Contract_BaseInterface {
    use CApp_Trait_BaseTrait;
}
```

This class can be used as-is or overridden with your own implementation via the `app.php` config:

```php
<?php
// application/{app_code}/default/config/app.php
return [
    'classes' => [
        'base' => CApp_Base::class,
    ],
];
```

---

### Creating Your Own Base Class

Most applications define their own base class with application-specific constants, methods, and overrides. Create it in your application's `libraries/` directory:

```php
<?php
// application/myapp/default/libraries/MyApp.php
class MyApp {
    use CApp_Trait_BaseTrait;

    const APPID = 100;
    const APPCODE = 'myapp';
    const APPNAME = 'My Application';
    const APPVERSION = '1.0';

    public static function isDevelopment() {
        return in_array(CF::domain(), [
            'myapp.dev.cresenity.com',
            'myapp.test',
        ]);
    }

    public static function isProduction() {
        return CF::domain() === 'myapp.com';
    }
}
```

Then use it throughout your controllers and libraries:

```php
<?php
class Controller_App_Home extends CController {
    public function index() {
        $user = MyApp::user();
        $orgId = MyApp::orgId();

        // ...
    }
}
```

---

### Application Context

Methods for accessing the current application and organization context:

```php
MyApp::appId();        // Application ID from domain config
MyApp::appCode();      // Application code (e.g. 'myapp')
MyApp::orgId();        // Organization ID from domain config
MyApp::orgCode();      // Organization code
MyApp::orgName();      // Organization name
MyApp::org();          // Organization model instance
```

---

### User and Authentication

Access the currently authenticated user:

```php
MyApp::user();         // Current user model instance (or null)
MyApp::userId();       // Current user ID
MyApp::username();     // Current username
MyApp::role();         // Current user role
MyApp::roleName();     // Current user role name
MyApp::isLogin();      // Check if user is logged in
MyApp::session();      // Get the session instance
```

---

### Request Data

Shorthand methods for accessing request data:

```php
MyApp::getRequest();       // Merged $_GET and $_POST
MyApp::getRequestGet();    // $_GET only
MyApp::getRequestPost();   // $_POST only
MyApp::getRequestFiles();  // $_FILES
```

> **Note:** For new code, prefer using `c::request()` which provides a more complete API. See [Request](/docs/basic/request).

---

### Environment

Check the current environment:

```php
MyApp::isDevelopment();   // true if running on development domain
MyApp::isStaging();       // true if running on staging domain
MyApp::isProduction();    // true if running on production domain
MyApp::environment();     // Returns 'development', 'staging', or 'production'
MyApp::protocol();        // 'http' or 'https'
MyApp::isMobile();        // true if request is from a mobile device
MyApp::remoteAddress();   // Client IP address
```

---

### Date and Time

```php
MyApp::now();                          // Current datetime as 'Y-m-d H:i:s'
MyApp::now('Y-m-d');                   // Current date with custom format

// Time travel (useful for testing)
MyApp::travelTo('2025-01-01');         // Set current time
MyApp::travelBack();                   // Reset to real time
```

---

### Permissions

Check user permissions:

```php
MyApp::havePermission('edit');              // true/false
MyApp::checkPermission('admin.settings');   // throws exception if denied
MyApp::notAccessible();                     // Abort with 403 Forbidden
```

---

### Default Audit Data

Helper methods that return audit data for database operations:

```php
MyApp::defaultInsert();
// ['created' => '2025-01-01 00:00:00', 'createdby' => 'username']

MyApp::defaultUpdate();
// ['updated' => '2025-01-01 00:00:00', 'updatedby' => 'username']

MyApp::defaultDelete();
// ['deleted' => '2025-01-01 00:00:00', 'deletedby' => 'username']
```

---

### Image Helpers

Generate placeholder and utility image URLs:

```php
MyApp::noImageUrl(200, 200);                       // Placeholder image
MyApp::transparentImageUrl(100, 100);              // Transparent pixel
MyApp::qrCodeImageUrl('https://example.com');      // QR code image
MyApp::gravatarImageUrl('user@example.com', 100);  // Gravatar avatar
MyApp::initialAvatarUrl('John Doe', 100);          // Initial-based avatar
```

---

### JSON Response

Return standardized JSON responses (commonly used in API controllers):

```php
return MyApp::jsonResponse(0, 'Success', ['id' => 1]);
// {"errCode": 0, "errMessage": "Success", "data": {"id": 1}}

return MyApp::toJsonResponse(1, 'Validation failed', $errors);
```
