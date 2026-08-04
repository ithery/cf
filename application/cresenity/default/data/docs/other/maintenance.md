# Maintenance

### Maintenance mode

Maintenance mode is enabled by adding a `down.php` file to the `data` folder.

An example `down.php`:

```php
<?php

return [
    'down' => false,
    'view' => 'system.maintenance',
    'cookie' => 'bypass-maintenance',
];
```

### The `down` setting

When set to `true`, the system is considered down and application logic is not executed. The
logic in `bootstrap.php` still runs.

### The `cookie` setting

Set this to a value that, when present as a cookie, bypasses maintenance mode. Omit the key
entirely if no bypass cookie should exist.

### The `view` setting

Defaults to `system.maintenance`. Override this key, or override
`views/system/maintenance.blade.php`, to change the maintenance page.
