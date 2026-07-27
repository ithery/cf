# CF 1.9 TODO

Tracks upgrade/refactor tasks for CF 1.9. Remove an item once it's done.

## High Priority

- Recurring: periodically review files under `modules/` and migrate/refactor them into `system/libraries` or `system/data`, or delete if dead. CF 1.9's target is for the `modules/` folder to disappear from the framework entirely — revisit this whenever touching code that references `modules/`.
- Remove `system/libraries/CComponent` and its `media/js/cres/src/element/component` / `media/js/cres/src/ui/component` implementation. It's a copy of Laravel Livewire and is targeted for removal in CF 1.9.

## Low Priority

- `CQueue_SerializableClosure` (`system/libraries/CQueue/SerializableClosure.php`) still `extends \Opis\Closure\SerializableClosure` directly (to hook `transformUseVariables()`/`resolveUseVariables()` for Eloquent-model-by-ID queue payloads) - deliberately left as-is during the 2026-07-27 `Opis\Closure\SerializableClosure` migration audit rather than rewritten onto `CFunction_SerializableClosure`'s composition-based `transformUseVariablesUsing()`/`resolveUseVariablesUsing()` static hooks, since it's used to serialize closures into queue jobs (`CQueue/CallQueuedClosure.php`, `Batch.php`, `PendingBatch.php`, `CModel/QuerySerializer.php`) that may already be sitting in a live queue backend - rewriting the class risks breaking already-enqueued payloads. The underlying "PHP reflection can't determine closure scope" crash this whole audit was about is now patched directly in the vendored `system/vendor/Opis/Closure/{SerializableClosure,ReflectionClosure}.php`, so this class (and any other lingering raw Opis usage) is protected regardless of which wrapper class it uses.
