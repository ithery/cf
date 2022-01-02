
<?php

/** @mixin \Illuminate\Testing\TestResponse */
class CComponent_Testing_TestableComponent {
    use CTrait_Macroable {
        __call as macroCall;
    }
    use CComponent_Testing_Concern_MakesAssertionsTrait;
    use CComponent_Testing_Concern_MakesCallsToComponentTrait;
    use CComponent_Testing_Concern_HasFunLittleUtilitiesTrait;

    public $payload = [];

    public $componentName;

    public $lastValidator;

    public $lastErrorBag;

    public $lastRenderedView;

    public $lastRenderedDom;

    public $lastResponse;

    public $rawMountedResponse;

    protected static $instancesById = [];

    public function __construct($name, $params = [], $queryParams = []) {
        CComponent_Manager::instance()->listen('view:render', function ($view) {
            $this->lastRenderedView = $view;
        });

        CComponent_Manager::instance()->listen('failed-validation', function ($validator) {
            $this->lastValidator = $validator;
        });

        CComponent_Manager::instance()->listen('component.hydrate.subsequent', function ($validator) {
            // Clear the validator held in memory from the last request so we
            // can properly assert validation errors for the most recent request.
            $this->lastValidator = null;
        });

        CComponent_Manager::instance()->listen('component.dehydrate', function ($component) {
            static::$instancesById[$component->id] = $component;

            $this->lastErrorBag = $component->getErrorBag();
        });

        CComponent_Manager::instance()->listen('mounted', function ($response) {
            $this->rawMountedResponse = $response;
        });

        // Don't actually generate S3 signedUrls during testing.
        // Can't use ::partialMock because it's not available in older versions of Laravel.
        $mock = Mockery::mock(GenerateSignedUploadUrl::class);
        $mock->makePartial()->shouldReceive('forS3')->andReturn([]);
        GenerateSignedUploadUrlFacade::swap($mock);

        // This allows the user to test a component by it's class name,
        // and not have to register an alias.
        if (class_exists($name)) {
            if (!is_subclass_of($name, Component::class)) {
                throw new \Exception('Class [' . $name . '] is not a subclass of Livewire\Component.');
            }

            $componentClass = $name;
            app('livewire')->component($name = c::str()->random(20), $componentClass);
        }

        $this->componentName = $name;

        $this->lastResponse = $this->pretendWereMountingAComponentOnAPage($name, $params, $queryParams);

        if (!$this->lastResponse->exception) {
            $this->updateComponent([
                'fingerprint' => $this->rawMountedResponse->fingerprint,
                'serverMemo' => $this->rawMountedResponse->memo,
                'effects' => $this->rawMountedResponse->effects,
            ], $isInitial = true);
        }
    }

    public function updateComponent($output, $isInitial = false) {
        // Sometimes Livewire will skip rendering the DOM.
        // We still want to be able to make assertions on
        // the currently rendered DOM. So we will store
        // the last known one.
        if ($output['effects']['html'] ?? false) {
            $this->lastRenderedDom = $output['effects']['html'];
        }

        if ($output['fingerprint'] ?? false) {
            $this->payload['fingerprint'] = $output['fingerprint'];
        }

        foreach ($output['serverMemo'] as $key => $newValue) {
            if ($key === 'data') {
                if ($isInitial) {
                    c::set($this->payload, 'serverMemo.data', []);
                }

                foreach ($newValue as $dataKey => $dataValue) {
                    c::set($this->payload, 'serverMemo.data.' . $dataKey, $dataValue);
                }

                continue;
            }

            if (!isset($this->payload['serverMemo'][$key])
                || $this->payload['serverMemo'][$key] !== $newValue
            ) {
                $this->payload['serverMemo'][$key] = $newValue;
            }
        }

        $this->payload['effects'] = $output['effects'];
    }

    public function pretendWereMountingAComponentOnAPage($name, $params, $queryParams) {
        $randomRoutePath = '/testing-livewire/' . c::str()->random(20);

        $this->registerRouteBeforeExistingRoutes($randomRoutePath, function () use ($name, $params) {
            return CView::factory()->file(__DIR__ . '/../views/mount-component.blade.php', [
                'name' => $name,
                'params' => $params,
            ]);
        });

        $laravelTestingWrapper = new MakesHttpRequestsWrapper(app());

        $response = null;

        $laravelTestingWrapper->temporarilyDisableExceptionHandlingAndMiddleware(function ($wrapper) use ($randomRoutePath, &$response, $queryParams) {
            $response = $wrapper->call('GET', $randomRoutePath, $queryParams);
        });

        return $response;
    }

    private function registerRouteBeforeExistingRoutes($path, $closure) {
        // To prevent this route from overriding wildcard routes registered within the application,
        // We have to make sure that this route is registered before other existing routes.
        $livewireTestingRoute = new CRouting_Route(['GET', 'HEAD'], $path, $closure);

        $existingRoutes = c::router()->getRoutes();

        // Make an empty collection.
        $runningCollection = new CRouting_RouteCollection();

        // Add this testing route as the first one.
        $runningCollection->add($livewireTestingRoute);

        // Now add the existing routes after it.
        foreach ($existingRoutes as $route) {
            $runningCollection->add($route);
        }

        // Now set this route collection as THE route collection for the app.
        app('router')->setRoutes($runningCollection);
    }

    public function pretendWereSendingAComponentUpdateRequest($message, $payload) {
        $result = $this->callEndpoint('POST', '/livewire/message/' . $this->componentName, [
            'fingerprint' => $this->payload['fingerprint'],
            'serverMemo' => $this->payload['serverMemo'],
            'updates' => [['type' => $message, 'payload' => $payload]],
        ]);

        CComponent_Manager::$isLivewireRequestTestingOverride = true;

        return $result;
    }

    public function callEndpoint($method, $url, $payload) {
        $laravelTestingWrapper = new MakesHttpRequestsWrapper(app());

        $response = null;

        $laravelTestingWrapper->temporarilyDisableExceptionHandlingAndMiddleware(function ($wrapper) use (&$response, $method, $url, $payload) {
            $response = $wrapper->call($method, $url, $payload);
        });

        return $response;
    }

    public function id() {
        return $this->payload['fingerprint']['id'];
    }

    public function instance() {
        return static::$instancesById[$this->id()];
    }

    public function viewData($key) {
        return $this->lastRenderedView->getData()[$key];
    }

    public function get($property) {
        return c::get(
            $this->instance(),
            $property,
            function () use ($property) {
                // If we couldn't find it, make sure it's not a computed property.
                $root = $this->instance()->beforeFirstDot($property);

                try {
                    $value = $this->instance()->{$root};
                } catch (\Throwable $e) {
                    if ($e instanceof PropertyNotFoundException) {
                        $value = null;
                    } elseif (c::str($e->getMessage())->contains('must not be accessed before initialization')) {
                        $value = null;
                    } else {
                        throw $e;
                    }
                }

                $nested = $root === $property ? null : $this->instance()->afterFirstDot($property);

                return c::get($value, $nested);
            }
        );
    }

    public function __get($property) {
        return $this->get($property);
    }

    public function __call($method, $params) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $params);
        }

        $this->lastResponse->$method(...$params);

        return $this;
    }
}
