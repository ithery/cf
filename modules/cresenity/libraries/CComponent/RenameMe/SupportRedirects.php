<?php




use Livewire\Redirector;

class CComponent_RenameMe_SupportRedirects
{
    static function init() { return new static; }

    public static $redirectorCacheStack = [];

    function __construct()
    {
        CComponent_Manager::instance()->listen('component.hydrate', function ($component, $request) {
            // Put Laravel's redirector aside and replace it with our own custom one.
            static::$redirectorCacheStack[] = CHTTP::redirector();

            CContainer::getInstance()->bind('redirect', function () use ($component) {
                $redirector = CComponent::manager()->redirector()->component($component);

                if (CSession::instance()) {
                    $redirector->setSession(CSession::instance());
                }

                return $redirector;
            });
        });

        CComponent_Manager::instance()->listen('component.dehydrate', function ($component, $response) {
            // Put the old redirector back into the container.
            CContainer::getInstance()->instance('redirect', array_pop(static::$redirectorCacheStack));

            // If there was no redirect. Clear flash session data.
            if (empty($component->redirectTo)) {
                CSession::instance()->forget(CSession::instance()->get('_flash.new'));

                return;
            }

            $response->effects['redirect'] = $component->redirectTo;
        });
    }
}
