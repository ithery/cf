# PHPCF - Tinker

### tinker

An interactive REPL against the running application. Models, the database connection,
configuration, and every helper are available as they are. Built on PsySH.

```
phpcf tinker
```

**Run it from inside the application folder.** The application code is derived from the folder
name through `CF::appCode()`, and that is what causes the application's own `bootstrap.php` to
be loaded during boot. No manual bootstrap call is needed.

```
cd application/ohayomart
phpcf tinker
```

Run from the framework root, the session opens in the framework context instead, without the
application's models or configuration.

### Executing code directly

The `--execute` option runs a single piece of code and exits. Useful for scripts and quick
checks:

```
phpcf tinker --execute='echo CF::appCode();'
```

### Including files first

Positional arguments include one or more files before the session starts:

```
phpcf tinker helper.php fixture.php
```

### Running longer scripts

Nested quoting breaks easily, especially when the command passes through `ssh` or `su -c`.
For anything longer than one line, write the code to a file and require it:

```
phpcf tinker --execute='require "/tmp/check.php";'
```

This also makes the script re-runnable without reassembling the quoting.

### Exploring data without leaving traces

A tinker session touches the real database. Wrap anything that writes in a transaction that is
rolled back, so the changes apply during inspection and then disappear:

```php
$db = c::db();
$db->begin();

$model = OHModel_Product::find(1);
$model->price = 5000;
$model->save();

// inspect the effects here

$db->rollback();
```

This is the most reliable way to confirm that a fix or a fixture behaves as expected against
real data before writing it into a test.

### Summarised output

Some types are displayed in a condensed form rather than as the full object:

| Class | Displayed as |
|---|---|
| `CModel` | attributes, changed attributes, and loaded relations |
| `CCollection` | the collection contents |
| `CBase_HtmlString` | the HTML string |
| `CBase_String` | the string value |

Additional casters may be registered through the `tinker.casters` configuration:

```php
// config/tinker.php
return [
    'casters' => [
        'OHModel_Order' => 'OHTinker_Caster::castOrder',
    ],
];
```

### CLI context limits

Tinker runs without an HTTP request, and parts of the framework depend on one:

- `CF::domain()` returns `{appCode}.test`, so `CF::getFile('navs', ...)` may miss and
  `CNavigation_Data::get()` returns an object instead of the nav array;
- nav renderers require route data (`getRouteData()`), so sidenav output cannot be tested from
  here — verify it in the browser;
- `$_SERVER['REQUEST_URI']` is absent, so code that reads it without a guard will fatal.

### Organisation context

Multi-tenant applications need an active organisation, which is normally resolved from the
domain. In CLI it has to be set explicitly. The helper name differs per application:

```php
OH::setOrgIdResolver(function () {
    return 12;
});
```

### PHP extensions

Tinker requires the `phar` extension. When the `php` binary on `PATH` does not have it, call a
complete binary explicitly:

```
/usr/local/lsws/lsphp84/bin/php $(which phpcf) tinker
```
