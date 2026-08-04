# PHPCF - Domain

Because CF serves multiple applications, the domain determines which application handles a
request. The commands on this page manage domain registrations and switch between domains when
working on the command line.

## Registration format

Each domain is a PHP file at `data/domain/{domain}.php` that returns an array:

```php
<?php

return [
    'app_id' => '2100',
    'app_code' => '3hweb',
    'org_id' => null,
    'org_code' => null,
];
```

`app_code` is what `CF::appCode()` reads when handling an HTTP request, which is how a domain
selects an application. `org_id` and `org_code` bind the domain to a single organisation in
multi-tenant applications, and are left empty when the organisation is resolved another way.

### domain:list

Lists every registered domain.

```
phpcf domain:list
```

### domain:create

Registers a new domain by writing its file into `data/domain`.

```
phpcf domain:create {domain}
```

Options:

- `--appId=` — `app_id` for this domain, defaults to `1`
- `--appCode=` — `app_code` for this domain, defaults to `cresenity`
- `--orgId=` — `org_id`, when the domain belongs to a single organisation
- `--orgCode=` — `org_code`, when the domain belongs to a single organisation

```
phpcf domain:create ohayomart.test --appId=2100 --appCode=ohayomart
```

### domain:delete

Removes a domain registration.

```
phpcf domain:delete {domain}
```

Only the registration file is deleted; the application files are left untouched.

### domain:switch

Sets the active domain for command line work. Subsequent commands run in that domain's
context.

```
phpcf domain:switch {domain}
```

The choice is stored in `data/current-domain` and **persists across sessions** — it applies
until changed, not only for the current terminal. The command refuses to switch to a domain
that is not registered, and reports when you are already on the requested domain.

## Relationship with the working directory

Application context is resolved two different ways on the command line, and they do not always
agree:

| Mechanism | Used by |
|---|---|
| working directory — `application/{code}/` | `phpcf tinker`, `phpcf test` |
| active domain — `data/current-domain` | commands run from the framework root |

When a command appears to run against the wrong application, check both: that you are in the
correct application folder, and that `domain:switch` points at the correct domain.
