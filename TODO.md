# CF 1.9 TODO

Tracks upgrade/refactor tasks for CF 1.9. Remove an item once it's done.

## High Priority

(none currently)

## Low Priority

- `modules/cresenity/config/client_modules.php`'s `blockly` module definition (`js`: `blockly_compressed.js`, `blocks_compressed.js`, `php_compressed.js`, `msg/js/en.js`, `element/blockly/blockly.js`) lives under the deprecated `modules/` folder — check whether it should move into `system/data/assets-module.php` so `modules/` can eventually be retired. It's not there yet — currently only defined in the deprecated location.
- `CTrait_Compat_Element_DataTable::export_excelxml_static()` (`system/libraries/CTrait/Compat/Element/DataTable.php:194`) declares optional `$sheet_name` before required `$table` — triggers a PHP deprecation warning on every call (implicit-required-after-optional is deprecated).
- `CDatabase_Query_Grammar` uses deprecated `${var}` string interpolation instead of `{$var}` — `system/libraries/CDatabase/Query/Grammar.php` lines 501, 1088, 1128.
