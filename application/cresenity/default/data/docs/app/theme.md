# Application - Theme

Themes control the visual appearance of your application — the HTML layout, CSS stylesheets, JavaScript libraries, and client-side modules that are loaded on every page.

### Theme Location

Theme files are stored in the `themes/` directory of your application:

```
application/{app_code}/default/themes/
```

Each theme is a PHP file that returns an array of configuration.

### Creating a Theme

Create a new theme file in your `themes/` directory. For example, `themes/my-theme.php`:

```php
<?php
return [
    'client_modules' => [
        'jquery',
        'bootstrap-4',
        'fontawesome-5-f',
        'select2',
        'toastr',
    ],
    'css' => [
        'cresenity.css',
        'my-theme/custom.css',
    ],
    'js' => [
        'cresenity.js',
        'my-theme/custom.js',
    ],
];
```

### Theme Configuration

| Key | Description |
|-----|------------|
| `client_modules` | Pre-built CSS/JS module bundles to load (e.g. `jquery`, `bootstrap-4`, `select2`) |
| `css` | CSS files to include (relative to the `media/css/` directory) |
| `js` | JavaScript files to include (relative to the `media/js/` directory) |

### Setting the Theme

Set the theme in your `bootstrap.php`:

```php
<?php
c::manager()->theme()->setTheme('my-theme');

// Or using the CApp shorthand
CApp::setTheme('my-theme');
```

You can also set it dynamically based on conditions:

```php
<?php
CManager::theme()->setThemeCallback(function ($theme) {
    if (cstr::startsWith(c::request()->path(), 'admin')) {
        return 'admin-theme';
    }
    return 'public-theme';
});
```

Or set it per-request in a controller:

```php
<?php
public function __construct() {
    parent::__construct();
    c::app()->setTheme('docs-theme');
}
```

### Default Theme

If no theme is set, the framework uses the built-in `cresenity` theme. The system theme file is located at `system/themes/null.php`.

### Theme Asset Paths

- **CSS files** are resolved from `media/css/`
- **JS files** are resolved from `media/js/`
- **Client modules** are pre-configured bundles that the framework manages (Bootstrap, jQuery, Font Awesome, etc.)

### Organizing Theme Assets

A common pattern for organizing theme-specific assets:

```
media/
├── css/
│   ├── cresenity.css           (framework base)
│   └── my-theme/
│       ├── custom.css
│       └── dashboard.css
└── js/
    ├── cresenity.js            (framework base)
    └── my-theme/
        ├── custom.js
        └── dashboard.js
```
