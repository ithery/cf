# .htaccess Protection

The framework docroot ships a root-level `.htaccess` that blocks a fixed set of
sensitive/internal paths from being served directly over HTTP — project docs
(`CLAUDE.md`, `TODO.md`, `BUG.md`, `README.md`, `CHANGELOG.md`), `.env`, lock/config files
(`composer.lock`, `phpstan.neon`, …), and `application/{app}/default/data/(key|vendor)/`.
Every app under the shared docroot inherits this automatically — nothing to configure.

Some apps additionally carry their own `.htaccess` for paths that are sensitive to that
app specifically and don't belong in the shared, framework-wide list — for example
`application/devcloud/.htaccess` (blocks its encryption keys under `default/data/key/` and
its encrypted DB-connection JSON under `default/json/`) or `application/tribelio/docs/.htaccess`
(blocks an entire internal-notes directory outright).

## Two things that will bite you

**Paths in a per-app `.htaccess` are relative to that file's own directory, not the shared
docroot root.** LiteSpeed and Apache strip the app's path prefix before matching rules in a
nested `.htaccess`, so a docroot-rooted pattern like the ones in the root `.htaccess`
(`^application/[^/]+/default/data/...`) will silently never match once pasted into a per-app
file — write the pattern as if that `.htaccess`'s own directory were the root instead
(`^default/data/key/`, not `^application/devcloud/default/data/key/`). Confirmed 2026-08-04:
the docroot-prefixed form blocked nothing; the relative form did.

**`Deny from all` / `Require all denied` do nothing on LiteSpeed for static files.** Neither
the legacy Apache 2.2 access directive nor its 2.4 replacement has any effect there for a
plain file request — use `RewriteRule <pattern> - [F,L]` (forbidden) or `[R=404,L]` (not
found) instead, the same rule family the root `.htaccess` already uses.

```apache
RewriteEngine On
RewriteRule ^default/data/key/ - [R=404,L]
```

## When the shared protection stops applying

The root `.htaccess`'s coverage (CLAUDE.md/TODO.md/BUG.md/.env/etc.) only reaches an app
while that app's HTTP requests actually pass through the shared docroot's `.htaccess` chain.
An app whose vhost is pointed at its own separate document root — historically the per-app
`default/public/` layout some apps used before it was rolled back framework-wide — stops
inheriting any of it, silently. If an app's docroot is ever decoupled from the shared one,
its own `.htaccess` needs to carry the same blocking rules (rewritten relative to its new
root per the gotcha above), or those files become servable again.

## One more LiteSpeed quirk worth knowing

LiteSpeed has been observed to silently stop processing a `.htaccess` after roughly 30
sequential `RewriteRule` lines — no error, the rules past that point just never fire. The
root `.htaccess` used to have one `RewriteRule` per blocked file (~38 of them) and the last
several silently never applied. Keep the list short by combining patterns into a few
alternating rules (`(CLAUDE|TODO|BUG|README|CHANGELOG)\.md$`) rather than one rule per file.
