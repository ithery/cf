<?php




use Livewire\ImplicitlyBoundMethod;

class CComponent_RenameMe_SupportComponentTraits
{
    static function init() { return new static; }

    protected $componentIdMethodMap = [];

    function __construct()
    {
        CComponent_Manager::instance()->listen('component.hydrate', function ($component) {
            $component->initializeTraits();

            foreach (c::classUsesRecursive($component) as $trait) {
                $hooks = [
                    'hydrate',
                    'mount',
                    'updating',
                    'updated',
                    'rendering',
                    'rendered',
                    'dehydrate',
                ];

                foreach ($hooks as $hook) {
                    $method = $hook.c::classBasename($trait);

                    if (method_exists($component, $method)) {
                        $this->componentIdMethodMap[$component->id][$hook][] = [$component, $method];
                    }
                }
            }

            $methods = $this->componentIdMethodMap[$component->id]['hydrate'] ?? [];

            foreach ($methods as $method) {
                ImplicitlyBoundMethod::call(app(), $method);
            }
        });

        CComponent_Manager::instance()->listen('component.mount', function ($component, $params) {
            $methods = $this->componentIdMethodMap[$component->id]['mount'] ?? [];

            foreach ($methods as $method) {
                ImplicitlyBoundMethod::call(app(), $method, $params);
            }
        });

        CComponent_Manager::instance()->listen('component.updating', function ($component, $name, $value) {
            $methods = $this->componentIdMethodMap[$component->id]['updating'] ?? [];

            foreach ($methods as $method) {
                ImplicitlyBoundMethod::call(app(), $method, [$name, $value]);
            }
        });

        CComponent_Manager::instance()->listen('component.updated', function ($component, $name, $value) {
            $methods = $this->componentIdMethodMap[$component->id]['updated'] ?? [];

            foreach ($methods as $method) {
                ImplicitlyBoundMethod::call(app(), $method, [$name, $value]);
            }
        });

        CComponent_Manager::instance()->listen('component.rendering', function ($component) {
            $methods = $this->componentIdMethodMap[$component->id]['rendering'] ?? [];

            foreach ($methods as $method) {
                ImplicitlyBoundMethod::call(app(), $method);
            }
        });

        CComponent_Manager::instance()->listen('component.rendered', function ($component, $view) {
            $methods = $this->componentIdMethodMap[$component->id]['rendered'] ?? [];

            foreach ($methods as $method) {
                ImplicitlyBoundMethod::call(app(), $method, [$view]);
            }
        });

        CComponent_Manager::instance()->listen('component.dehydrate', function ($component) {
            $methods = $this->componentIdMethodMap[$component->id]['dehydrate'] ?? [];

            foreach ($methods as $method) {
                ImplicitlyBoundMethod::call(app(), $method);
            }
        });
    }
}
