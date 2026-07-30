# Cresenity Framework (CF)

Custom PHP framework, not Laravel. PHP >= 7.4 — no PHP 8.1+ syntax (`str_contains`, `match`, `??=`, enums, readonly, union types, named args, first-class callables).

Branches: `master` (main), `development`. Docs: `application/cresenity/default/data/docs/`

## Commands

```bash
phpcf test                               # Tests (from root=framework, from app dir=app tests)
phpcf tinker --execute='...'             # REPL against real DB/models (from app dir); call {App}Bootstrap::boot() first
./system/vendor/PHPStan/phpstan analyse  # PHPStan level 4
php-cs-fixer fix                         # Code format
cd media/js/cres && npm run build        # JS build
```

Framework tests in `tests/`, app tests in `application/{app}/default/tests/`.

`phpcf tinker`: run from the app dir (e.g. `application/ohayomart/`). Call `{App}Bootstrap::boot()` first or non-underscore classes throw "Class not found". Fake org context with `OH::setOrgIdResolver(fn() => $orgId)` (per-app helper name varies). Wrap anything that writes in `$db = c::db(); $db->begin(); ...; $db->rollback();` to explore/verify against live data without leaving traces — invaluable for validating a bug fix or test fixture against real framework behavior before writing it into a test file.

## Code Style

**PHP**: same-line braces, 4 spaces, single quotes, camelCase methods, C-prefixed StudlyCaps classes (CApp, CModel). PHPDoc `@var`/`@param`/`@return` required on all properties/methods.

**JS**: follow `.eslintrc`, `const`/`let` only (no `var`). **CSS**: follow `.stylelintrc`.

## Conventions

- `system/vendor/` manually managed — no composer install
- `env.php` has secrets — never commit
- `application/` folders are separate repos (gitignored except `cresenity`)
- Controller files lowercase; `return $app` from controllers (not `echo $app->render()`)
- `modules/` DEPRECATED — use `system/libraries`
- For bulk repetitive edits (e.g. converting 100+ entries), write a bash script instead of editing one-by-one to save quota/tokens
- `TODO.md` (root) tracks CF 1.9 upgrade/refactor tasks — remove an item once it's done; blocked in `.htaccess` like `CLAUDE.md`
