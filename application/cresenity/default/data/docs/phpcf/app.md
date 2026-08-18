# PHPCF - Application

### init

An interactive wizard that creates a new application. It must be run from inside the
application's own folder (`application/{code}/`) — the application code is taken from that
folder name and is not asked for. The wizard asks for the application class prefix, which is
used to generate the base library (for example `OH.php` for an application with the `OH`
prefix), and optionally an admin preset, then generates the project scaffolding.

```
cd application/propmind
phpcf init
```

The following options may be supplied to skip the corresponding wizard question:

```
phpcf init --domain= --prefix= --title= --admin
```

- `--domain` — the local domain for this application, defaults to `{code}.test`
- `--prefix` — the class prefix for the application base library (for example `PM`); `CF` is
  not allowed
- `--title` — the application title, defaults to the application code
- `--admin` — scaffold the admin preset without asking

### app:code

Displays or sets the application code for the active domain.

```
phpcf app:code {appCode?}
```

Called without an argument it reports the current code. Called with one, it writes the new
value into the active domain's registration file and reports the result:

```
phpcf app:code ohayomart
```

Since the value is stored per domain, the change follows whichever domain is currently active.
See [Domain](/docs/phpcf/domain) for how the active domain is selected.
