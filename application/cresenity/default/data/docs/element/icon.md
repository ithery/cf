# Element - Icon

The `CElement_Component_Icon` component renders an icon element. It supports icon fonts (Font Awesome, Themify, etc.) and custom SVG icon directories.

Add an icon using `addIcon()`:

```php
$app = c::app();
$app->addIcon()->setIcon('ti-home');

return $app;
```

---

### Icon Fonts

Use any icon class from the icon libraries loaded by your theme:

```php
$app->addIcon()->setIcon('ti-user');        // Themify Icons
$app->addIcon()->setIcon('fas fa-cog');     // Font Awesome 5
$app->addIcon()->setIcon('lnr lnr-heart');  // Linearicons
```

The icon prefix (e.g. `icon icon-`) is configured in your theme via `icon.prefix`.

---

### SVG Icons

Register a custom SVG icon directory and reference icons using dot notation:

```php
// In bootstrap.php
c::manager()->icon()->registerIconDirectory('myapp', DOCROOT . 'application/myapp/default/media/img/icons/');

// In controller
$app->addIcon()->setIcon('myapp.settings');  // loads settings.svg from the directory
```

---

### Inline Usage

Icons are commonly used inside other elements:

```php
$btn = $app->addAction();
$btn->setIcon('ti-pencil');
$btn->setLabel('Edit');

$widget = $app->addWidget();
$widget->setIcon('ti-layers');
$widget->setTitle('Dashboard');
```
