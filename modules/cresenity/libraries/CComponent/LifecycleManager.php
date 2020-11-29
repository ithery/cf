<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_LifecycleManager {

    protected static $hydrationMiddleware = [];
    protected static $initialHydrationMiddleware = [];
    protected static $initialDehydrationMiddleware = [];
    public $request;
    public $instance;
    public $response;

    public static function fromSubsequentRequest($payload) {
        return c::tap(new static, function ($instance) use ($payload) {
                    $instance->request = new CComponent_Request($payload);
                    $instance->instance = CComponent_Manager::instance()->getInstance($instance->request->name(), $instance->request->id());
                });
    }

    /**
     * 
     * @param string $name
     * @param string $id
     * @return CComponent_LifecycleManager
     */
    public static function fromInitialRequest($name, $id) {
        return c::tap(new static, function ($instance) use ($name, $id) {
                    $instance->instance = CComponent_Manager::instance()->getInstance($name, $id);
                    $instance->request = new CComponent_Request([
                        'fingerprint' => ['id' => $id, 'name' => $name, 'locale' => CF::getLocale()],
                        'updates' => [],
                        'serverMemo' => [],
                    ]);
                });
    }

    public static function fromInitialInstance($component) {
        $name = CComponent_Manager::instance()->getAlias(get_class($component), $component->getName());

        return c::tap(new static, function ($instance) use ($component, $name) {
                    $instance->instance = $component;
                    $instance->request = new CComponent_Request([
                        'fingerprint' => ['id' => $component->id, 'name' => $name, 'locale' => CF::getLocale()],
                        'updates' => [],
                        'serverMemo' => [],
                    ]);
                });
    }

    public static function registerHydrationMiddleware(array $classes) {
        static::$hydrationMiddleware += $classes;
    }

    public static function registerInitialHydrationMiddleware(array $callables) {
        static::$initialHydrationMiddleware += $callables;
    }

    public static function registerInitialDehydrationMiddleware(array $callables) {
        static::$initialDehydrationMiddleware += $callables;
    }

    public function hydrate() {
        foreach (static::$hydrationMiddleware as $class) {
            $class::hydrate($this->instance, $this->request);
        }

        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function initialHydrate() {
        foreach (static::$initialHydrationMiddleware as $callable) {
            $callable($this->instance, $this->request);
        }

        return $this;
    }

    /**
     * 
     * @param array $params
     * @return $thiss
     */
    public function mount($params = []) {
        // Assign all public component properties that have matching parameters.
        c::collect(array_intersect_key($params, $this->instance->getPublicPropertiesDefinedBySubClass()))
                ->each(function ($value, $property) {
                    $this->instance->{$property} = $value;
                });

        if (method_exists($this->instance, 'mount')) {
            try {
                CComponent_ImplicitlyBoundMethod::call(app(), [$this->instance, 'mount'], $params);
            } catch (CValidation_Exception $e) {
                CComponent_Manager::instance()->dispatch('failed-validation', $e->validator);

                $this->instance->setErrorBag($e->validator->errors());
            }
        }

        CComponent_Manager::instance()->dispatch('component.mount', $this->instance, $params);

        return $this;
    }

    public function renderToView() {
        $this->instance->renderToView();

        return $this;
    }

    public function initialDehydrate() {
        $this->response = CComponent_Response::fromRequest($this->request);

        foreach (array_reverse(static::$initialDehydrationMiddleware) as $callable) {
            $callable($this->instance, $this->response);
        }

        return $this;
    }

    public function dehydrate() {
        $this->response = CComponent_Response::fromRequest($this->request);

        // The array is being reversed here, so the middleware dehydrate phase order of execution is
        // the inverse of hydrate. This makes the middlewares behave like layers in a shell.
        foreach (array_reverse(static::$hydrationMiddleware) as $class) {
            $class::dehydrate($this->instance, $this->response);
        }

        return $this;
    }

    public function toInitialResponse() {
        $this->response->embedThyselfInHtml();

        CComponent_Manager::instance()->dispatch('mounted', $this->response);

        return $this->response->toInitialResponse();
    }

    public function toSubsequentResponse() {
        return $this->response->toSubsequentResponse();
    }

}
