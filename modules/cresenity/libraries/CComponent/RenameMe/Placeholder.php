<?php





class CComponent_RenameMe_Placeholder
{
    static function init() { return new static; }

    function __construct()
    {
        CComponent_Manager::instance()->listen('component.hydrate', function ($component, $request) {
            //
        });

        CComponent_Manager::instance()->listen('component.dehydrate', function ($component, $response) {
            //
        });
    }
}
