# Cres JS - UI

UI utilities provided by the `cresenity.ui` object.

### Waves Effect

Add a Material Design ripple/waves effect to clickable elements.

Configure in `config/cresjs.php`:

```php
<?php
return [
    'waves' => [
        'selector' => '.btn',
    ],
];
```

All elements matching the selector (`.btn` in this example) will have the waves effect applied automatically.

If not configured, the default selector is `.cres-waves-effect`.

### Theme Mode

Toggle between light and dark themes:

```js
// Access via cresenity.theme
cresenity.theme.localStorageKey; // key used to persist theme preference
```

Theme mode is initialized automatically on page load and persisted in localStorage.
