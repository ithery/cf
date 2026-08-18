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
    'secret' => c::env('MAINTENANCE_SECRET', ''),
];
```

Set `down` back to `false` to bring the application up again. The file may be left in place
permanently — it is the `down` key that decides, not the presence of the file.

A file that is missing the `down` key, or that does not return an array at all, leaves the
application **up**. A mistake in this file should not cost downtime.

## Configuration keys

### down

Whether the application is offline.

```php
'down' => true,
```

Defaults to `false` when the key is absent. When `false`, no other key is read.

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

### secret

A random token that turns a URL into a bypass link, so a developer can check the application
while it is closed to everyone else.

```php
'secret' => c::env('MAINTENANCE_SECRET', ''),
```

Opening `https://your-site/{secret}` once sets a cookie and redirects to the home page. That
browser then sees the application normally for 12 hours; every other visitor keeps getting the
maintenance page.

```
https://example.com/a7f3c9e21b4d5680
```

**The cookie's value is compared against the secret**, using a timing-safe comparison — unlike
the legacy `cookie` key below, where only the presence of a cookie is checked.

Read it from `env.php` rather than writing it into the file. It is a credential, and a
credential committed to a repository stops being secret the moment someone reads the code.
Generate a fresh one per incident:

```
php -r 'echo bin2hex(random_bytes(16)), "\n";'
```

Leave it empty when no bypass link should exist. An empty secret never matches a request path,
so it cannot accidentally open the site.

### cookie

The name of a cookie that bypasses maintenance mode, so the site stays reachable for whoever
is doing the work.

```php
'cookie' => 'bypass-maintenance',
```

> This is the older mechanism, kept so existing configurations keep working. Prefer `secret`.

**Only the presence of the cookie is checked, not its value.** Any value — including an empty
one — lets the request through, so the cookie *name* is the secret. That is the weakness:
a name cannot be rotated per incident, is usually identical across applications, and is
normally committed to the repository.

Omit the key entirely when no bypass should exist. Read it from `env.php` when it is used.

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
