# Cresenity Framework (CF)

Custom PHP framework supporting 80+ applications in a single installation. Not Laravel — has its own conventions.

## Quick Reference

- **PHP**: >= 7.4 (no PHP 8.1+ syntax in framework code)
- **Main branch**: `master`, development on `development`
- **Entry point**: `index.php` → `system/core/Bootstrap.php` → `CF::setup()`
- **CLI**: `php cf <command>` (e.g. `php cf app:create myapp`)

## Architecture

### Autoloading (not PSR-4)

Underscore `_` maps to directory separator. No namespaces in framework code.

- Uppercase first char → `system/libraries/` (e.g. `CApp_Base` → `libraries/CApp/Base.php`)
- Lowercase first char → `system/helpers/` (e.g. `carr` → `helpers/carr.php`)
- `Controller_` prefix → `controllers/` (e.g. `Controller_App_Home` → `controllers/app/home.php`)

### File Lookup Order

1. `application/{app_code}/{org_code}/` — org override
2. `application/{app_code}/default/` — app base
3. `system/` — framework core

### Key Directories

```
system/core/         Core classes (CF, CFRouter, CFConfig, etc.)
system/libraries/    Framework libraries (CApp, CModel, CDatabase, CRouting, etc.)
system/helpers/      Helper classes (c, carr, cstr, curl)
system/vendor/       Third-party libraries (NOT composer-managed, committed to git)
system/config/       Default config files
application/         One subfolder per app, each with default/ containing controllers/views/config
modules/             DEPRECATED — do not add new code here, migrate to system/libraries
media/js/cres/src/   Cres.js source (Rollup-bundled, exposed as window.cresenity)
```

### CElement Components (PHP + JS)

Components with client-side behavior have a PHP class in `system/libraries/CElement/Component/` and a corresponding JS module in `media/js/cres/src/element/component/`. The PHP side renders HTML with a `cres-element` attribute and `cres-config` JSON. The JS side auto-initializes via `initComponent()` in `element/component/index.js`.

```
PHP: system/libraries/CElement/Component/Repeater.php    → renders <div cres-element="component:Repeater" cres-config="...">
JS:  media/js/cres/src/element/component/Repeater/       → Repeater.js, updater.js, index.js, index.scss
```

Components with JS: ShowMore, Shimmer, Repeater, Gallery, ProgressBar, Nestable, Image, CountDownTimer.
Components without JS (to be migrated to cres.js): Widget, Form, Alert, Accordion, Action, Tab, Chart, Icon, ListGroup, Tooltip, PrismCode, Kanban, TreeView, PdfViewer, FileManager. These currently inline JS via PHP `js()` method or rely on external plugins — migrate them to `media/js/cres/src/element/component/` following the pattern above.

When adding a new component with JS behavior:
1. Create PHP class in `system/libraries/CElement/Component/` — set `cres-element` and `cres-config` attrs in `build()`
2. Create JS module in `media/js/cres/src/element/component/{Name}/` — export `init{Name}` and class
3. Register in `media/js/cres/src/element/component/index.js` — import and add to `initComponent()` switch + `component` export
4. Run `npm run dev` to rebuild

### Routing

Auto-discovers controllers from URL. Explicit routes (`c::router()->get(...)`) take priority.
Verb-prefixed methods restrict HTTP verbs: `getDetail()` = GET only, `postStore()` = POST only, unprefixed = all verbs.
See `system/libraries/CRouting/RouteFinder.php`.

### Common Patterns

```php
// Controller returning CApp (page builder)
$app = c::app();
$app->title('Page Title');
$app->addTable()->setDataFromModel(UserModel::class);
return $app;

// Helpers
c::app()          // CApp singleton
c::request()      // CHTTP_Request
c::config('key')  // Config value
c::env('KEY')     // Env variable from application/{app}/env.php
c::auth()         // Auth guard
c::view('name')   // Blade view
c::redirect()     // Redirector
c::url('path')    // URL helper
```

## Commands

```bash
# Tests
./system/vendor/PHPUnit/phpunit          # Run tests (bootstrap: system/core/Bootstrap.php)

# Static analysis
./system/vendor/PHPStan/phpstan analyse  # PHPStan level 4

# Code formatting (globally installed)
php-cs-fixer fix                         # Uses .php-cs-fixer.dist.php

# JavaScript
npm run dev                              # Build JS assets
npm run watch                            # Watch mode
npm run prod                             # Production build
```

## Code Style

### PHP
- **Braces**: same line for classes, methods, and control structures
- **Indentation**: 4 spaces
- **Quotes**: single quotes
- **Method names**: camelCase
- **Class names**: StudlyCaps with C-prefix (CApp, CModel, CDatabase)
- **No comments** unless the WHY is non-obvious
- Config: `.php-cs-fixer.dist.php`, `.editorconfig`, `phpcs.xml`

### JavaScript
All JS in `media/js/cres/src/` MUST comply with `.eslintrc` — read that file before writing any JS.
Use `const` for values never reassigned, `let` otherwise — never `var`.
Build after changes: `cd media/js/cres && npm run build`

## Important Conventions

- `system/vendor/` is manually managed — do NOT run composer install into it
- `env.php` contains secrets — never commit
- `application/` folders are separate git repos (gitignored except `cresenity`)
- Controller filenames are lowercase; class segments after `Controller_` map to path
- Always `return $app` from controllers, never `echo $app->render()` (deprecated)
- PHP 7.4 compatible: no `str_contains()`, no `match`, no `??=`, no first-class callables, no named args, no enums, no readonly, no union types in framework code

## Documentation

Docs are in `application/cresenity/default/data/docs/` as Markdown files.
`###` (h3) headings become the right sidebar submenu (extracted by `Cresenity\Documentation\Renderer`).
Docs should be written in English.
