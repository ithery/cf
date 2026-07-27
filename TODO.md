# CF 1.9 TODO

Tracks upgrade/refactor tasks for CF 1.9. Remove an item once it's done.

## High Priority

- Recurring: periodically review files under `modules/` and migrate/refactor them into `system/libraries` or `system/data`, or delete if dead. CF 1.9's target is for the `modules/` folder to disappear from the framework entirely — revisit this whenever touching code that references `modules/`.
- Remove `system/libraries/CComponent` and its `media/js/cres/src/element/component` / `media/js/cres/src/ui/component` implementation. It's a copy of Laravel Livewire and is targeted for removal in CF 1.9.
- Finish auditing remaining `Opis\Closure\SerializableClosure` usages (`CJavascript.php`, `CObservable/Javascript.php`, `CElement/Component/ActionRow.php`, `CQueue/SerializableClosure(Factory).php`, `CApp/Concern/BreadcrumbTrait.php`, `CManager/DataProvider/{Collection,Closure,Sql}DataProvider.php`, `CManager/Transform/*`, `CAjax/Engine/{Validation,ImgUpload}.php`, `CDatabase/Driver/Mysqli.php`, `CRouting/Route.php`, `CFunction.php`) and migrate them to `CFunction_SerializableClosure` for consistency - only `CElement/Component/Form.php`, `CNotification/ChannelAbstract.php`, `CTrait/Element/Transform.php`, `CValidation/Rule.php`, `CException/ContextAbstract.php`, and `CManager/DataProvider/ModelDataProvider.php` have been migrated so far. `CManager_DataProvider_ClosureDataProvider` (used by `setDataFromClosure()`) is one of the still-unmigrated ones - `tests/Ajax/SelectSearchClosureRoundTripTest.php` calls this out explicitly since it exercises that exact class without being able to prove it's safe from the same bug class yet.

## Low Priority

(all clear for now)
