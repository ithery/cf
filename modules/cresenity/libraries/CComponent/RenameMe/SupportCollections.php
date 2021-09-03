<?php



use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;


class CComponent_RenameMe_SupportCollections
{
    static function init() { return new static; }

    function __construct()
    {
        CComponent_Manager::instance()->listen('property.dehydrate', function ($name, $value, $component, $response) {
            if (! $value instanceof Collection || $value instanceof EloquentCollection) return;


        });

        CComponent_Manager::instance()->listen('property.hydrate', function ($name, $value, $component, $request) {
            $collections = data_get($request->memo, 'dataMeta.collections', []);

            foreach ($collections as $name) {
                data_set($component, $name, collect(data_get($component, $name)));
            }
        });
    }
}
