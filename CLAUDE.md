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

The CLI is **`phpcf`**. The `cf` file sitting in the docroot is a **0-byte legacy stub** — that
is normal and expected, not a broken install. It runs, prints nothing, and exits 0, which reads
exactly like a working command that found nothing to do; don't diagnose a dead CLI from it.

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
- **`setDataFromArray()` and `setAjax()` are mutually exclusive on a table.** `CElement_Component_DataTable::requery()` wipes `$this->data` the moment ajax mode is on, and the ajax request then falls through to the `Query` processor with an empty query, producing invalid SQL — `select * from () as a`. It fails as a 500 on the ajax call, not on the page render, so it is easy to ship. Array-backed tables need no ajax at all: the default client-side DataTable still paginates, sorts, and searches. Confirmed 2026-08-15 from two production exceptions (#12109 smartfield, #12110 landmap). Several apps still pair the two — that combination is latent breakage, not a working pattern
- For bulk repetitive edits (e.g. converting 100+ entries), write a bash script instead of editing one-by-one to save quota/tokens
- `docs/` holds local working notes and is **gitignored** — `docs/TODO.md` tracks CF 1.9 upgrade/refactor tasks; remove an item once it's done. Blocked in `.htaccess` like `CLAUDE.md`, and no longer part of the repo, so it exists only on machines that create it
- **Per-app `.htaccess` files** (e.g. `application/devcloud/.htaccess`, `application/tribelio/docs/.htaccess`) are a real, existing pattern for blocking extra app-specific sensitive paths beyond what the root `.htaccess` already covers. Two gotchas, both confirmed 2026-08-04:
  - **Paths inside a per-app `.htaccess` are relative to that `.htaccess`'s own directory**, not the shared docroot root — LiteSpeed/Apache strip the app prefix before matching, so a docroot-rooted pattern (like the ones in the root `.htaccess`) silently never matches when pasted into a per-app file.
  - **A per-app `.htaccess` is silently dead when the vhost's own rewrite block is already near ~30 rules.** LiteSpeed appends the `.htaccess` rules *after* the vhost's, and stops processing after roughly the 30th — so on a vhost carrying the old 34-rule block, every rule in the per-app file is dropped without a word. Proven 2026-08-15 with one controlled test: the same file, same docroot, same `.htaccess` returned **404 through `devcloud.cresenity.com`** (19-rule vhost) and **200 through `aid.cresenity.com`** (34-rule vhost). So when a per-app `.htaccess` "doesn't work", count the vhost's rules before doubting the pattern — apply the CF standard block via devcloud's *Apply Rewrite Rule For CF* first, then the `.htaccess` starts working on its own.
  - **`Deny from all` / `Require all denied` do nothing on LiteSpeed for static files.** Use `RewriteRule ... [F,L]` (or `[R=404,L]`) instead — same rule family the root `.htaccess` already uses to block `CLAUDE.md`/`TODO.md`/`BUG.md`/`README.md`/`CHANGELOG.md`, `.env`, and `default/data/key|vendor/`.
  - That root-level protection only applies while the app shares the top-level docroot. An app whose vhost points at its own separate docroot (e.g. a `default/public/` layout) stops inheriting it entirely and needs the same blocking rules copied into its own `.htaccess` — this bit aidnity historically (see `project-cf19-public-docroot-rollback` memory).
