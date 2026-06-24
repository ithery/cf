# Application - Navigation

Navigation files define the sidebar or top menu structure of your application. CApp automatically renders the navigation based on these definitions.

### Navigation Location

Navigation files are stored in the `navs/` directory of your application:

```
application/{app_code}/default/navs/
```

The default navigation file is `nav.php`. You can create additional navigation files (e.g. `admin.php`, `docs.php`) and load them by name.

### Basic Navigation

Create a `navs/nav.php` file that returns an array of navigation items:

```php
<?php
return [
    [
        'name' => 'dashboard',
        'label' => c::__('Dashboard'),
        'uri' => 'home/index',
        'icon' => 'home',
    ],
    [
        'name' => 'users',
        'label' => c::__('Users'),
        'uri' => 'admin/user/index',
        'icon' => 'ti-user',
    ],
    [
        'name' => 'settings',
        'label' => c::__('Settings'),
        'uri' => 'admin/setting/index',
        'icon' => 'ti-settings',
    ],
];
```

### Navigation Item Properties

| Property | Description |
|----------|------------|
| `name` | Unique identifier for the navigation item |
| `label` | Display text (use `c::__()` for translation support) |
| `uri` | Target URL path (e.g. `home/index`) |
| `icon` | Icon class name (e.g. `home`, `ti-user`, `lnr lnr-cog`) |
| `subnav` | Array of child navigation items (for nested menus) |

### Nested Navigation

Create multi-level menus using the `subnav` property:

```php
<?php
return [
    [
        'name' => 'dashboard',
        'label' => c::__('Dashboard'),
        'uri' => 'app/home/index',
    ],
    [
        'name' => 'master',
        'label' => c::__('Master Data'),
        'subnav' => [
            [
                'name' => 'master.products',
                'label' => c::__('Products'),
                'uri' => 'admin/master/product/index',
            ],
            [
                'name' => 'master.categories',
                'label' => c::__('Categories'),
                'uri' => 'admin/master/category/index',
            ],
        ],
    ],
];
```

### Organizing Navigation Files

For large navigation structures, split your navigation into separate files and include them:

**`navs/admin.php`**
```php
<?php
return [
    [
        'name' => 'dashboard',
        'label' => c::__('Dashboard'),
        'uri' => 'app/home/index',
    ],
    [
        'name' => 'master',
        'label' => c::__('Master Data'),
        'subnav' => include __DIR__ . '/admin/master.php',
    ],
    [
        'name' => 'reports',
        'label' => c::__('Reports'),
        'subnav' => include __DIR__ . '/admin/reports.php',
    ],
];
```

**`navs/admin/master.php`**
```php
<?php
return [
    [
        'name' => 'master.products',
        'label' => c::__('Products'),
        'uri' => 'admin/master/product/index',
    ],
    [
        'name' => 'master.categories',
        'label' => c::__('Categories'),
        'uri' => 'admin/master/category/index',
    ],
];
```

### Loading Navigation in CApp

Set which navigation to use from your controller:

```php
<?php
$app = c::app();
$app->setNav('admin');   // loads navs/admin.php

return $app;
```

### Custom Navigation Renderer

Override how the navigation is rendered using a custom callback:

```php
<?php
$app->setNavRenderer(function ($navs) use ($category, $page) {
    return c::view('custom.nav', [
        'navs' => $navs,
        'category' => $category,
        'page' => $page,
    ])->render();
});
```
