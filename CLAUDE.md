# Cresenity Framework (CF)

Custom PHP framework supporting 80+ applications in a single installation. Not Laravel — has its own conventions.

## Quick Reference

- **PHP**: >= 7.4 (no PHP 8.1+ syntax in framework code)
- **Main branch**: `master`, development on `development`
- **Entry point**: `index.php` → `system/core/Bootstrap.php` → `CF::setup()`
- **CLI**: `phpcf <command>` (e.g. `phpcf app:create myapp`), installed via `composer global require cresenity/phpcf`

## Documentation

Read framework docs in `application/cresenity/default/data/docs/` for architecture, coding patterns, routing, controllers, helpers, elements, components, and more. Key docs:

- `basic/` — autoloading, bootstrap, controller, routing, request, view
- `starter/` — installation, configuration, directory structure
- `app/` — CApp setup, auth, navigation, elements, themes
- `element/` — all CElement components (table, form, widget, chart, etc.)
- `forminput/` — form input types (select, datetime, file upload, etc.)
- `helper/` — c, carr, cstr, curl helpers
- `cresjs/` — Cres.js client-side framework
- `command/` — CLI commands
- `phpcf/` — model, testing, tinker, devsuite

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
- **PHPDoc required**: all properties must have `@var`, all methods must have `@param`/`@return`. Intelephense warnings for missing type info (P1132) must be resolved
- Config: `.php-cs-fixer.dist.php`, `.editorconfig`, `phpcs.xml`

### JavaScript
All JS in `media/js/cres/src/` MUST comply with `.eslintrc` — read that file before writing any JS.
Use `const` for values never reassigned, `let` otherwise — never `var`.
Build after changes: `cd media/js/cres && npm run build`

### CSS
All CSS MUST comply with `.stylelintrc` — read that file before writing any CSS.

## Important Conventions

- `system/vendor/` is manually managed — do NOT run composer install into it
- `env.php` contains secrets — never commit
- `application/` folders are separate git repos (gitignored except `cresenity`)
- Controller filenames are lowercase; class segments after `Controller_` map to path
- Always `return $app` from controllers, never `echo $app->render()` (deprecated)
- PHP 7.4 compatible: no `str_contains()`, no `match`, no `??=`, no first-class callables, no named args, no enums, no readonly, no union types in framework code
- `modules/` is DEPRECATED — do not add new code here, migrate to `system/libraries`
