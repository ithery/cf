# CF 1.9 TODO

Tracks upgrade/refactor tasks for CF 1.9. Remove an item once it's done.

## High Priority

- Recurring: periodically review files under `modules/` and migrate/refactor them into `system/libraries` or `system/data`, or delete if dead. CF 1.9's target is for the `modules/` folder to disappear from the framework entirely — revisit this whenever touching code that references `modules/`.
- Remove `system/libraries/CComponent` and its `media/js/cres/src/element/component` / `media/js/cres/src/ui/component` implementation. It's a copy of Laravel Livewire and is targeted for removal in CF 1.9.

## Low Priority

- PHP 8.2+ deprecation noise seen at runtime (found 2026-07-27 while testing
  aidnity's plan/subscription feature via `phpcf tinker`), fix by switching to
  `{$var}` interpolation / null-coalescing where relevant:
  - `system/libraries/CModel/Trait/QueriesRelationships.php:444` - `"${name} ${function} ${column}"` (deprecated `${var}` syntax).
  - `system/libraries/CModel/Nested/Query.php:332` - `"${lft} = ${rgt} - 1"` (same).
  - `system/libraries/CModel/Nested/Query.php:669` - `"... ${table} as {$waInterm}"` (same, mixed with valid `{$var}` on the same line).
  - `system/libraries/CApp.php:201` - `strlen(CF::orgCode())` warns "Passing null to parameter #1 of type string is deprecated" when `CF::orgCode()` returns null; needs a `(string)` cast or null check.
  - `system/libraries/CApp/SEO/MetaTags.php:159` - `"<title>${title}</title>"` (deprecated `${var}` syntax, found 2026-07-29 while testing ohayomart's customer manage page via `phpcf tinker`).
  - `system/libraries/CApp/Config.php:44` - `strlen($domain)` warns "Passing null to parameter #1 of type string is deprecated" when `$domain` is passed null explicitly; needs a `(string)` cast or null check (same day/session as above).

- `CQueue_SerializableClosure` (`system/libraries/CQueue/SerializableClosure.php`) still `extends \Opis\Closure\SerializableClosure` directly (to hook `transformUseVariables()`/`resolveUseVariables()` for Eloquent-model-by-ID queue payloads) - deliberately left as-is during the 2026-07-27 `Opis\Closure\SerializableClosure` migration audit rather than rewritten onto `CFunction_SerializableClosure`'s composition-based `transformUseVariablesUsing()`/`resolveUseVariablesUsing()` static hooks, since it's used to serialize closures into queue jobs (`CQueue/CallQueuedClosure.php`, `Batch.php`, `PendingBatch.php`, `CModel/QuerySerializer.php`) that may already be sitting in a live queue backend - rewriting the class risks breaking already-enqueued payloads. The underlying "PHP reflection can't determine closure scope" crash this whole audit was about is now patched directly in the vendored `system/vendor/Opis/Closure/{SerializableClosure,ReflectionClosure}.php`, so this class (and any other lingering raw Opis usage) is protected regardless of which wrapper class it uses.
