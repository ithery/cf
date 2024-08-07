<?php

final class CAuth_Access_Gate implements CAuth_Contract_GateInterface {
    use CAuth_Access_Concern_HandleAuthorizationTrait;

    /**
     * The user resolver callable.
     *
     * @var callable
     */
    protected $userResolver;

    /**
     * All of the defined abilities.
     *
     * @var array
     */
    protected $abilities = [];

    /**
     * All of the defined policies.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * All of the registered before callbacks.
     *
     * @var array
     */
    protected $beforeCallbacks = [];

    /**
     * All of the registered after callbacks.
     *
     * @var array
     */
    protected $afterCallbacks = [];

    /**
     * All of the defined abilities using class@method notation.
     *
     * @var array
     */
    protected $stringCallbacks = [];

    /**
     * The callback to be used to guess policy names.
     *
     * @var null|callable
     */
    protected $guessPolicyNamesUsingCallback;

    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Create a new gate instance.
     *
     * @param array         $abilities
     * @param array         $policies
     * @param array         $beforeCallbacks
     * @param array         $afterCallbacks
     * @param null|callable $guessPolicyNamesUsingCallback
     *
     * @return void
     */
    public function __construct(
        array $abilities = [],
        array $policies = [],
        array $beforeCallbacks = [],
        array $afterCallbacks = [],
        callable $guessPolicyNamesUsingCallback = null
    ) {
        $this->policies = $policies;
        $this->abilities = $abilities;
        $this->userResolver = function () {
            return call_user_func(CAuth::manager()->userResolver());
        };
        $this->afterCallbacks = $afterCallbacks;
        $this->beforeCallbacks = $beforeCallbacks;
        $this->guessPolicyNamesUsingCallback = $guessPolicyNamesUsingCallback;
    }

    /**
     * Determine if a given ability has been defined.
     *
     * @param string|array $ability
     *
     * @return bool
     */
    public function has($ability) {
        $abilities = is_array($ability) ? $ability : func_get_args();

        foreach ($abilities as $ability) {
            if (!isset($this->abilities[$ability])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Define a new ability.
     *
     * @param string          $ability
     * @param callable|string $callback
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function define($ability, $callback) {
        if (is_array($callback) && isset($callback[0]) && is_string($callback[0])) {
            $callback = $callback[0] . '@' . $callback[1];
        }

        if (is_callable($callback)) {
            $this->abilities[$ability] = $callback;
        } elseif (is_string($callback)) {
            $this->stringCallbacks[$ability] = $callback;

            $this->abilities[$ability] = $this->buildAbilityCallback($ability, $callback);
        } else {
            throw new InvalidArgumentException("Callback must be a callable or a 'Class@method' string.");
        }

        return $this;
    }

    /**
     * Define abilities for a resource.
     *
     * @param string     $name
     * @param string     $class
     * @param null|array $abilities
     *
     * @return $this
     */
    public function resource($name, $class, array $abilities = null) {
        $abilities = $abilities ?: [
            'viewAny' => 'viewAny',
            'view' => 'view',
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete',
        ];

        foreach ($abilities as $ability => $method) {
            $this->define($name . '.' . $ability, $class . '@' . $method);
        }

        return $this;
    }

    /**
     * Create the ability callback for a callback string.
     *
     * @param string $ability
     * @param string $callback
     *
     * @return \Closure
     */
    protected function buildAbilityCallback($ability, $callback) {
        return function () use ($ability, $callback) {
            if (cstr::contains($callback, '@')) {
                list($class, $method) = cstr::parseCallback($callback);
            } else {
                $class = $callback;
            }

            $policy = $this->resolvePolicy($class);

            $arguments = func_get_args();

            $user = array_shift($arguments);

            $result = $this->callPolicyBefore(
                $policy,
                $user,
                $ability,
                $arguments
            );

            if (!is_null($result)) {
                return $result;
            }

            return isset($method)
                    ? $policy->{$method}(...func_get_args())
                    : $policy(...func_get_args());
        };
    }

    /**
     * Define a policy class for a given class type.
     *
     * @param string $class
     * @param string $policy
     *
     * @return $this
     */
    public function policy($class, $policy) {
        $this->policies[$class] = $policy;

        return $this;
    }

    /**
     * Register a callback to run before all Gate checks.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function before(callable $callback) {
        $this->beforeCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register a callback to run after all Gate checks.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function after(callable $callback) {
        $this->afterCallbacks[] = $callback;

        return $this;
    }

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param string      $ability
     * @param array|mixed $arguments
     *
     * @return bool
     */
    public function allows($ability, $arguments = []) {
        return $this->check($ability, $arguments);
    }

    /**
     * Determine if the given ability should be denied for the current user.
     *
     * @param string      $ability
     * @param array|mixed $arguments
     *
     * @return bool
     */
    public function denies($ability, $arguments = []) {
        return !$this->allows($ability, $arguments);
    }

    /**
     * Determine if all of the given abilities should be granted for the current user.
     *
     * @param iterable|string $abilities
     * @param array|mixed     $arguments
     *
     * @return bool
     */
    public function check($abilities, $arguments = []) {
        return c::collect($abilities)->every(function ($ability) use ($arguments) {
            return $this->inspect($ability, $arguments)->allowed();
        });
    }

    /**
     * Determine if any one of the given abilities should be granted for the current user.
     *
     * @param iterable|string $abilities
     * @param array|mixed     $arguments
     *
     * @return bool
     */
    public function any($abilities, $arguments = []) {
        return c::collect($abilities)->contains(function ($ability) use ($arguments) {
            return $this->check($ability, $arguments);
        });
    }

    /**
     * Determine if all of the given abilities should be denied for the current user.
     *
     * @param iterable|string $abilities
     * @param array|mixed     $arguments
     *
     * @return bool
     */
    public function none($abilities, $arguments = []) {
        return !$this->any($abilities, $arguments);
    }

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param string      $ability
     * @param array|mixed $arguments
     *
     * @throws \CAuth_Exception_AuthorizationException
     *
     * @return \CAuth_Access_Response
     */
    public function authorize($ability, $arguments = []) {
        return $this->inspect($ability, $arguments)->authorize();
    }

    /**
     * Inspect the user for the given ability.
     *
     * @param string      $ability
     * @param array|mixed $arguments
     *
     * @return \CAuth_Access_Response
     */
    public function inspect($ability, $arguments = []) {
        try {
            $result = $this->raw($ability, $arguments);

            if ($result instanceof CAuth_Access_Response) {
                return $result;
            }

            return $result ? CAuth_Access_Response::allow() : CAuth_Access_Response::deny();
        } catch (CAuth_Exception_AuthorizationException $e) {
            return $e->toResponse();
        }
    }

    /**
     * Get the raw result from the authorization callback.
     *
     * @param string      $ability
     * @param array|mixed $arguments
     *
     * @throws \CAuth_Exception_AuthorizationException
     *
     * @return mixed
     */
    public function raw($ability, $arguments = []) {
        $arguments = carr::wrap($arguments);

        $user = $this->resolveUser();

        // First we will call the "before" callbacks for the Gate. If any of these give
        // back a non-null response, we will immediately return that result in order
        // to let the developers override all checks for some authorization cases.
        $result = $this->callBeforeCallbacks(
            $user,
            $ability,
            $arguments
        );

        if (is_null($result)) {
            $result = $this->callAuthCallback($user, $ability, $arguments);
        }

        // After calling the authorization callback, we will call the "after" callbacks
        // that are registered with the Gate, which allows a developer to do logging
        // if that is required for this application. Then we'll return the result.
        return c::tap($this->callAfterCallbacks(
            $user,
            $ability,
            $arguments,
            $result
        ), function ($result) use ($user, $ability, $arguments) {
            $this->dispatchGateEvaluatedEvent($user, $ability, $arguments, $result);
        });
    }

    /**
     * Determine whether the callback/method can be called with the given user.
     *
     * @param null|\CAuth_AuthenticatableInterface $user
     * @param \Closure|string|array                $class
     * @param null|string                          $method
     *
     * @return bool
     */
    protected function canBeCalledWithUser($user, $class, $method = null) {
        if (!is_null($user)) {
            return true;
        }

        if (!is_null($method)) {
            return $this->methodAllowsGuests($class, $method);
        }

        if (is_array($class)) {
            $className = is_string($class[0]) ? $class[0] : get_class($class[0]);

            return $this->methodAllowsGuests($className, $class[1]);
        }

        return $this->callbackAllowsGuests($class);
    }

    /**
     * Determine if the given class method allows guests.
     *
     * @param string $class
     * @param string $method
     *
     * @return bool
     */
    protected function methodAllowsGuests($class, $method) {
        try {
            $reflection = new ReflectionClass($class);

            $method = $reflection->getMethod($method);
        } catch (Exception $e) {
            return false;
        }
        /** @phpstan-ignore-next-line */
        if ($method) {
            $parameters = $method->getParameters();

            return isset($parameters[0]) && $this->parameterAllowsGuests($parameters[0]);
        }
        /** @phpstan-ignore-next-line */
        return false;
    }

    /**
     * Determine if the callback allows guests.
     *
     * @param callable $callback
     *
     * @throws \ReflectionException
     *
     * @return bool
     */
    protected function callbackAllowsGuests($callback) {
        $parameters = (new ReflectionFunction($callback))->getParameters();

        return isset($parameters[0]) && $this->parameterAllowsGuests($parameters[0]);
    }

    /**
     * Determine if the given parameter allows guests.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return bool
     */
    protected function parameterAllowsGuests($parameter) {
        return ($parameter->hasType() && $parameter->allowsNull())
               || ($parameter->isDefaultValueAvailable() && is_null($parameter->getDefaultValue()));
    }

    /**
     * Resolve and call the appropriate authorization callback.
     *
     * @param null|\CAuth_AuthenticatableInterface $user
     * @param string                               $ability
     * @param array                                $arguments
     *
     * @return bool
     */
    protected function callAuthCallback($user, $ability, array $arguments) {
        $callback = $this->resolveAuthCallback($user, $ability, $arguments);

        return $callback($user, ...$arguments);
    }

    /**
     * Call all of the before callbacks and return if a result is given.
     *
     * @param null|\CAuth_AuthenticatableInterface $user
     * @param string                               $ability
     * @param array                                $arguments
     *
     * @return null|bool
     */
    protected function callBeforeCallbacks($user, $ability, array $arguments) {
        foreach ($this->beforeCallbacks as $before) {
            if (!$this->canBeCalledWithUser($user, $before)) {
                continue;
            }

            if (!is_null($result = $before($user, $ability, $arguments))) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Call all of the after callbacks with check result.
     *
     * @param \CAuth_AuthenticatableInterface $user
     * @param string                          $ability
     * @param array                           $arguments
     * @param bool                            $result
     *
     * @return null|bool
     */
    protected function callAfterCallbacks($user, $ability, array $arguments, $result) {
        foreach ($this->afterCallbacks as $after) {
            if (!$this->canBeCalledWithUser($user, $after)) {
                continue;
            }

            $afterResult = $after($user, $ability, $result, $arguments);

            $result = $result ?: $afterResult;
        }

        return $result;
    }

    /**
     * Dispatch a gate evaluation event.
     *
     * @param null|\CAuth_AuthenticatableInterface $user
     * @param string                               $ability
     * @param array                                $arguments
     * @param null|bool                            $result
     *
     * @return void
     */
    protected function dispatchGateEvaluatedEvent($user, $ability, array $arguments, $result) {
        CEvent::dispatcher()->dispatch(new CAuth_Access_Event_GateEvaluated($user, $ability, $result, $arguments));
    }

    /**
     * Resolve the callable for the given ability and arguments.
     *
     * @param null|\CAuth_AuthenticatableInterface $user
     * @param string                               $ability
     * @param array                                $arguments
     *
     * @return callable
     */
    protected function resolveAuthCallback($user, $ability, array $arguments) {
        if (isset($arguments[0])
            && !is_null($policy = $this->getPolicyFor($arguments[0]))
            && $callback = $this->resolvePolicyCallback($user, $ability, $arguments, $policy)
        ) {
            return $callback;
        }

        if (isset($this->stringCallbacks[$ability])) {
            list($class, $method) = cstr::parseCallback($this->stringCallbacks[$ability]);

            if ($this->canBeCalledWithUser($user, $class, $method ?: '__invoke')) {
                return $this->abilities[$ability];
            }
        }

        if (isset($this->abilities[$ability])
            && $this->canBeCalledWithUser($user, $this->abilities[$ability])
        ) {
            return $this->abilities[$ability];
        }

        return function () {
        };
    }

    /**
     * Get a policy instance for a given class.
     *
     * @param object|string $class
     *
     * @return mixed
     */
    public function getPolicyFor($class) {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!is_string($class)) {
            return;
        }

        if (isset($this->policies[$class])) {
            return $this->resolvePolicy($this->policies[$class]);
        }

        foreach ($this->guessPolicyName($class) as $guessedPolicy) {
            if (class_exists($guessedPolicy)) {
                return $this->resolvePolicy($guessedPolicy);
            }
        }

        foreach ($this->policies as $expected => $policy) {
            if (is_subclass_of($class, $expected)) {
                return $this->resolvePolicy($policy);
            }
        }
    }

    /**
     * Guess the policy name for the given class.
     *
     * @param string $class
     *
     * @return array
     */
    protected function guessPolicyName($class) {
        if ($this->guessPolicyNamesUsingCallback) {
            return carr::wrap(call_user_func($this->guessPolicyNamesUsingCallback, $class));
        }

        $classDirname = str_replace('/', '\\', dirname(str_replace('\\', '/', $class)));

        $classDirnameSegments = explode('\\', $classDirname);

        return carr::wrap(CCollection::times(count($classDirnameSegments), function ($index) use ($class, $classDirnameSegments) {
            $classDirname = implode('\\', array_slice($classDirnameSegments, 0, $index));

            return $classDirname . '\\Policies\\' . c::classBasename($class) . 'Policy';
        })->reverse()->values()->first(function ($class) {
            return class_exists($class);
        }) ?: [$classDirname . '\\Policies\\' . c::classBasename($class) . 'Policy']);
    }

    /**
     * Specify a callback to be used to guess policy names.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function guessPolicyNamesUsing(callable $callback) {
        $this->guessPolicyNamesUsingCallback = $callback;

        return $this;
    }

    /**
     * Build a policy class instance of the given type.
     *
     * @param object|string $class
     *
     * @throws \CContainer_Exception_BindingResolutionException
     *
     * @return mixed
     */
    public function resolvePolicy($class) {
        return c::container()->make($class);
    }

    /**
     * Resolve the callback for a policy check.
     *
     * @param \CAuth_AuthenticatableInterface $user
     * @param string                          $ability
     * @param array                           $arguments
     * @param mixed                           $policy
     *
     * @return bool|callable
     */
    protected function resolvePolicyCallback($user, $ability, array $arguments, $policy) {
        if (!is_callable([$policy, $this->formatAbilityToMethod($ability)])) {
            return false;
        }

        return function () use ($user, $ability, $arguments, $policy) {
            // This callback will be responsible for calling the policy's before method and
            // running this policy method if necessary. This is used to when objects are
            // mapped to policy objects in the user's configurations or on this class.
            $result = $this->callPolicyBefore(
                $policy,
                $user,
                $ability,
                $arguments
            );

            // When we receive a non-null result from this before method, we will return it
            // as the "final" results. This will allow developers to override the checks
            // in this policy to return the result for all rules defined in the class.
            if (!is_null($result)) {
                return $result;
            }

            $method = $this->formatAbilityToMethod($ability);

            return $this->callPolicyMethod($policy, $method, $user, $arguments);
        };
    }

    /**
     * Call the "before" method on the given policy, if applicable.
     *
     * @param mixed                           $policy
     * @param \CAuth_AuthenticatableInterface $user
     * @param string                          $ability
     * @param array                           $arguments
     *
     * @return mixed
     */
    protected function callPolicyBefore($policy, $user, $ability, $arguments) {
        if (!method_exists($policy, 'before')) {
            return;
        }

        if ($this->canBeCalledWithUser($user, $policy, 'before')) {
            return $policy->before($user, $ability, ...$arguments);
        }
    }

    /**
     * Call the appropriate method on the given policy.
     *
     * @param mixed                                $policy
     * @param string                               $method
     * @param null|\CAuth_AuthenticatableInterface $user
     * @param array                                $arguments
     *
     * @return mixed
     */
    protected function callPolicyMethod($policy, $method, $user, array $arguments) {
        // If this first argument is a string, that means they are passing a class name
        // to the policy. We will remove the first argument from this argument array
        // because this policy already knows what type of models it can authorize.
        if (isset($arguments[0]) && is_string($arguments[0])) {
            array_shift($arguments);
        }

        if (!is_callable([$policy, $method])) {
            return;
        }

        if ($this->canBeCalledWithUser($user, $policy, $method)) {
            return $policy->{$method}($user, ...$arguments);
        }
    }

    /**
     * Format the policy ability into a method name.
     *
     * @param string $ability
     *
     * @return string
     */
    protected function formatAbilityToMethod($ability) {
        return strpos($ability, '-') !== false ? cstr::camel($ability) : $ability;
    }

    /**
     * Get a gate instance for the given user.
     *
     * @param \CAuth_AuthenticatableInterface|mixed $user
     *
     * @return static
     */
    public function forUser($user) {
        $callback = function () use ($user) {
            return $user;
        };

        return new static(
            $this->abilities,
            $this->policies,
            $this->beforeCallbacks,
            $this->afterCallbacks,
            $this->guessPolicyNamesUsingCallback
        );
    }

    /**
     * Resolve the user from the user resolver.
     *
     * @return mixed
     */
    protected function resolveUser() {
        return call_user_func($this->userResolver);
    }

    /**
     * Get all of the defined abilities.
     *
     * @return array
     */
    public function abilities() {
        return $this->abilities;
    }

    /**
     * Get all of the defined policies.
     *
     * @return array
     */
    public function policies() {
        return $this->policies;
    }
}
