<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
abstract class CComponent {

    use CTrait_Macroable {
        __call as macroCall;
    }

    use CComponent_Concern_ValidatesInputTrait,
        CComponent_Concern_HandlesActionsTrait,
        CComponent_Concern_ReceivesEventsTrait,
        CComponent_Concern_PerformsRedirectsTrait,
        CComponent_Concern_TracksRenderedChildrenTrait,
        CComponent_Concern_InteractsWithPropertiesTrait;

    public $id;
    protected $queryString = [];
    protected $computedPropertyCache = [];
    protected $initialLayoutConfiguration = [];
    protected $shouldSkipRender = false;
    protected $preRenderedView;

    public function __construct($id = null) {
        $this->id = $id ? $id : cstr::random(20);

        $this->ensureIdPropertyIsntOverridden();
    }

    public function __invoke(CContainer_Container $container, Route $route) {
        $componentParams = (new ImplicitRouteBinding($container))
                ->resolveAllParameters($route, $this);

        $manager = LifecycleManager::fromInitialInstance($this)
                ->initialHydrate()
                ->mount($componentParams)
                ->renderToView();

        $layoutType = isset($this->initialLayoutConfiguration['type']) ? $this->initialLayoutConfiguration['type'] : 'component';

        return app('view')->file(__DIR__ . "/Macros/livewire-view-{$layoutType}.blade.php", [
                    'view' => isset($this->initialLayoutConfiguration['view']) ? $this->initialLayoutConfiguration['view'] : 'layouts.app',
                    'params' => isset($this->initialLayoutConfiguration['params']) ? $this->initialLayoutConfiguration['params'] : [],
                    'slotOrSection' => isset($this->initialLayoutConfiguration['slotOrSection']) ? $this->initialLayoutConfiguration['slotOrSection'] : [
                'extends' => 'content', 'component' => 'default',
                    ][$layoutType],
                    'manager' => $manager,
        ]);
    }

    protected function ensureIdPropertyIsntOverridden() {
        c::throwIf(
                array_key_exists('id', $this->getPublicPropertiesDefinedBySubClass()),
                new CComponent_Exception_CannotUseReservedComponentProperties('id', $this::getName())
        );
    }

    public function initializeTraits() {
        foreach (c::classUsesRecursive($class = static::class) as $trait) {
            if (method_exists($class, $method = 'initialize' . c::classBasename($trait))) {
                $this->{$method}();
            }
        }
    }

    public static function getName() {
        /**
        $namespace = c::collect(explode('.', str_replace(['/', '\\'], '.', c::config('component.class_namespace', 'App\\Http\\Livewire'))))
                ->map([cstr::class, 'kebab'])
                ->implode('.');
                * 
         */
        $namespace = '';
        $fullName = c::collect(explode('.', str_replace(['/', '\\'], '.', static::class)))
                ->map([cstr::class, 'kebab'])
                ->implode('.');
        
        if (cstr::startsWith($fullName, $namespace)) {
            return (string) cstr::substr($fullName, strlen($namespace) + 1);
        }

        return $fullName;
    }

    public function getQueryString() {
        return $this->queryString;
    }

    public function skipRender() {
        $this->shouldSkipRender = true;
    }

    public function renderToView() {
        CComponent_Manager::instance()->dispatch('component.rendering', $this);

        $view = method_exists($this, 'render') ? CContainer::getInstance()->call([$this, 'render']) : CView::factory("component.{$this::getName()}");

        if (is_string($view)) {
            $view = CView_Factory::instance()->make(CreateBladeView::fromString($view));
        }

        c::throwUnless($view instanceof CView_View,
                new \Exception('"render" method on [' . get_class($this) . '] must return instance of [' . CView_View::class . ']'));

        // Get the layout config from the view.
        if ($view->livewireLayout) {
            $this->initialLayoutConfiguration = $view->livewireLayout;
        }

        CComponent_Manager::instance()->dispatch('component.rendered', $this, $view);

        return $this->preRenderedView = $view;
    }

    public function output($errors = null) {
        if ($this->shouldSkipRender)
            return null;

        $view = $this->preRenderedView;

        // In the service provider, we hijack Laravel's Blade engine
        // with our own. However, we only want Livewire hijackings,
        // while we're rendering Livewire components. So we'll
        // activate it here, and deactivate it at the end
        // of this method.
        $engine = CView::engineResolver()->resolve('blade');
        $engine->startComponentRendering($this);

        $this->setErrorBag(
                $errorBag = $errors ?: carr::get($view->getData(),'errors',$this->getErrorBag())
        );

        $previouslySharedErrors = carr::get(CView::factory()->getShared(),'errors',new CBase_ViewErrorBag);
        $previouslySharedInstance = carr::get(CView::factory()->getShared(),'_instance', null);

        $errors = (new CBase_ViewErrorBag)->put('default', $errorBag);

        $errors->getBag('default')->merge(
                $previouslySharedErrors->getBag('default')
        );

        $view->with([
            'errors' => $errors,
            '_instance' => $this,
                ] + $this->getPublicPropertiesDefinedBySubClass());

        CView::factory()->share('errors', $errors);
        CView::factory()->share('_instance', $this);

        $output = $view->render();

        CView::factory()->share('errors', $previouslySharedErrors);
        CView::factory()->share('_instance', $previouslySharedInstance);

        CComponent_Manager::instance()->dispatch('view:render', $view);

        $engine->endComponentRendering();

        return $output;
    }

    public function normalizePublicPropertiesForJavaScript() {
        foreach ($this->getPublicPropertiesDefinedBySubClass() as $key => $value) {
            if (is_array($value)) {
                $this->$key = $this->reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder($value);
            }

            if ($value instanceof EloquentCollection) {
                // Preserve collection items order by reindexing underlying array.
                $this->$key = $value->values();
            }
        }
    }

    public function forgetComputed($key = null) {
        if (is_null($key)) {
            $this->computedPropertyCache = [];
            return;
        }

        $keys = is_array($key) ? $key : func_get_args();

        collect($keys)->each(function ($i) {
            if (isset($this->computedPropertyCache[$i])) {
                unset($this->computedPropertyCache[$i]);
            }
        });
    }

    public function __get($property) {
        $studlyProperty = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $property)));

        if (method_exists($this, $computedMethodName = 'get' . $studlyProperty . 'Property')) {
            if (isset($this->computedPropertyCache[$property])) {
                return $this->computedPropertyCache[$property];
            }

            return $this->computedPropertyCache[$property] = app()->call([$this, $computedMethodName]);
        }

        throw new CComponent_Exception_PropertyNotFoundException($property, static::getName());
    }

    public function __call($method, $params) {
        if (
                in_array($method, ['mount', 'hydrate', 'dehydrate', 'updating', 'updated']) || c::str($method)->startsWith(['updating', 'updated', 'hydrate', 'dehydrate'])
        ) {
            // Eat calls to the lifecycle hooks if the dev didn't define them.
            return;
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $params);
        }

        throw new BadMethodCallException(sprintf(
                        'Method %s::%s does not exist.', static::class, $method
        ));
    }

}
