# Maintenance

Maintenance mode takes an application offline without stopping the web server or
undeploying anything. While it is on, every HTTP request is answered with a maintenance page
and `503 Service Unavailable`; queue workers stop picking up jobs as well.

## Enabling it

Create `default/data/down.php` in the application and return an array:

```php
<?php

return [
    'down' => true,
    'view' => 'system.maintenance',
    'cookie' => 'bypass-maintenance',
];
```

Set `down` back to `false` to bring the application up again. The file may be left in place
permanently — it is the `down` key that decides, not the presence of the file.

> **A file that returns an array without a `down` key puts the application into maintenance
> mode.** The key defaults to `true` when it is missing, so an incomplete file takes the site
> offline rather than leaving it up. Always write the key explicitly.

## Configuration keys

### down

Whether the application is offline.

```php
'down' => true,
```

Defaults to `true` when the key is absent. When `false`, no other key is read.

### view

The view rendered as the maintenance page.

```php
'view' => 'system.maintenance',
```

Defaults to `system.maintenance`, which resolves to
`system/views/system/maintenance.blade.php`. The view receives the whole configuration array
as `$data`, so extra keys may be added and read from the template:

```php
// down.php
return [
    'down' => true,
    'until' => '18:00 WIB',
];
```

```blade
{{-- your own view --}}
<p>Back at {{ carr::get($data, 'until') }}</p>
```

Point `view` at your own view, or override `system/views/system/maintenance.blade.php`, to
change the page.

### cookie

The name of a cookie that bypasses maintenance mode, so the site stays reachable for whoever
is doing the work.

```php
'cookie' => 'bypass-maintenance',
```

**Only the presence of the cookie is checked, not its value.** Any value — including an empty
one — lets the request through, so treat the cookie name itself as the secret and pick
something that cannot be guessed.

Omit the key entirely when no bypass should exist.

Setting the bypass cookie in the browser console:

```js
document.cookie = 'bypass-maintenance=1; path=/';
```

## What still runs

Maintenance mode is checked in `CHTTP_Kernel::handle()`, before routing and before any
controller is reached. What that means in practice:

| | While in maintenance |
|---|---|
| `bootstrap.php` | **runs** — it executes during framework boot, ahead of the check |
| Routes and controllers | not reached |
| Queue workers | stop picking up jobs (`CQueue_Runner`) |
| `phpcf` commands | unaffected |

Because `bootstrap.php` still runs, anything it does — registering events, opening
connections, scheduling — happens on every request even while the site is down. Keep that in
mind if the reason for the maintenance is a failing dependency that `bootstrap.php` touches.

The queue worker check can be overridden per run:

```
phpcf queue:work --force
```

## Where the file is looked up

The file is resolved with `CF::findFile('data', 'down')`, which searches the application's
`default/data/` directory first and the framework's afterwards. In practice this means:

```
application/{app}/default/data/down.php
```

Each application therefore has its own maintenance switch. Taking one application offline does
not affect the others sharing the same framework installation.

## Response status

The maintenance page is served with `503 Service Unavailable`, which is the correct status for
a temporary outage — search engines treat it as "come back later" rather than as a removal,
and uptime monitors report it as downtime rather than as a broken page.
