# Cresenity Framework (CF)

Custom PHP framework, not Laravel. PHP >= 7.4 — no PHP 8.1+ syntax (`str_contains`, `match`, `??=`, enums, readonly, union types, named args, first-class callables).

Branches: `master` (main), `development`. Docs: `application/cresenity/default/data/docs/`

## Commands

```bash
phpcf test                               # Tests (from root=framework, from app dir=app tests)
phpcf tinker --execute='...'             # REPL against real DB/models (run from the app dir)
phpcf phpstan <path>                     # PHPStan level 4 (one path per run)
php-cs-fixer fix                         # Code format
cd media/js/cres && npm run build        # JS build
```

Framework tests in `tests/`, app tests in `application/{app}/default/tests/`.

`phpcf tinker`: run from the app dir (e.g. `application/ohayomart/`). **No manual bootstrap call is needed** — `CF::appCode()` derives the app from the working directory, so `CF::loadBootstrapFiles()` includes that app's `bootstrap.php` (and whatever it calls, e.g. `DBootstrap::boot()`) during boot. Verified 2026-08-02 from three different app dirs, each resolving to its own app. Fake org context with `OH::setOrgIdResolver(fn() => $orgId)` (per-app helper name varies). Wrap anything that writes in `$db = c::db(); $db->begin(); ...; $db->rollback();` to explore/verify against live data without leaving traces — invaluable for validating a bug fix or test fixture against real framework behavior before writing it into a test file.

## Code Style

**PHP**: same-line braces, 4 spaces, single quotes, camelCase methods, C-prefixed StudlyCaps classes (CApp, CModel). PHPDoc `@var`/`@param`/`@return` required on all properties/methods.

**JS**: follow `.eslintrc`, `const`/`let` only (no `var`). **CSS**: follow `.stylelintrc`.

**Alpine.js is bundled inside `cres.js`** — `media/js/cres/src/Cresenity.js` does
`import Alpine from 'alpinejs'` (^3.9.5) and the build exposes `window.Alpine`. So Alpine
directives (`x-data`, `x-show`, …) work on **any** page that loads cres.js; do **not**
register an `alpine` asset module for them. `SupportAlpine()` in cres.js is the
Cresenity↔Alpine bridge (re-scans after each cres request, morphdom integration) — it
guards on `window.Alpine` because it runs alongside the bundle, not because Alpine is
optional. A standalone `media/js/libs/alpine.js` module also exists; it is legacy and
would double-load Alpine.

## Conventions

- `system/vendor/` manually managed — no composer install
- `env.php` has secrets — never commit
- `application/` folders are separate repos (gitignored except `cresenity`)
- Controller files lowercase; `return $app` from controllers (not `echo $app->render()`)
- `modules/` DEPRECATED — use `system/libraries`
- For bulk repetitive edits (e.g. converting 100+ entries), write a bash script instead of editing one-by-one to save quota/tokens
- `docs/` holds local working notes and is **gitignored** — `docs/TODO.md` tracks CF 1.9 upgrade/refactor tasks; remove an item once it's done. Blocked in `.htaccess` like `CLAUDE.md`, and no longer part of the repo, so it exists only on machines that create it
- **Per-app `.htaccess` files** (e.g. `application/devcloud/.htaccess`, `application/tribelio/docs/.htaccess`) are a real, existing pattern for blocking extra app-specific sensitive paths beyond what the root `.htaccess` already covers. Two gotchas, both confirmed 2026-08-04:
  - **Paths inside a per-app `.htaccess` are relative to that `.htaccess`'s own directory**, not the shared docroot root — LiteSpeed/Apache strip the app prefix before matching, so a docroot-rooted pattern (like the ones in the root `.htaccess`) silently never matches when pasted into a per-app file.
  - **`Deny from all` / `Require all denied` do nothing on LiteSpeed for static files.** Use `RewriteRule ... [F,L]` (or `[R=404,L]`) instead — same rule family the root `.htaccess` already uses to block `CLAUDE.md`/`TODO.md`/`BUG.md`/`README.md`/`CHANGELOG.md`, `.env`, and `default/data/key|vendor/`.
  - That root-level protection only applies while the app shares the top-level docroot. An app whose vhost points at its own separate docroot (e.g. a `default/public/` layout) stops inheriting it entirely and needs the same blocking rules copied into its own `.htaccess` — this bit aidnity historically (see `project-cf19-public-docroot-rollback` memory).
