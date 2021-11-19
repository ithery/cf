<?php

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class CBroadcast_BroadcasterAbstract implements CBroadcast_Contract_BroadcasterInterface {
    /**
     * The registered channel authenticators.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * The registered channel options.
     *
     * @var array
     */
    protected $channelOptions = [];

    /**
     * The binding registrar instance.
     *
     * @var \CRouting_Contract_BindingRegistrarInterface
     */
    protected $bindingRegistrar;

    /**
     * Register a channel authenticator.
     *
     * @param \CBroadcast_Contract_HasBroadcastChannelInterface|string $channel
     * @param callable|string                                          $callback
     * @param array                                                    $options
     *
     * @return $this
     */
    public function channel($channel, $callback, $options = []) {
        if ($channel instanceof CBroadcast_Contract_HasBroadcastChannelInterface) {
            $channel = $channel->broadcastChannelRoute();
        } elseif (is_string($channel) && class_exists($channel) && is_a($channel, CBroadcast_Contract_HasBroadcastChannelInterface::class, true)) {
            $channel = (new $channel())->broadcastChannelRoute();
        }

        $this->channels[$channel] = $callback;

        $this->channelOptions[$channel] = $options;

        return $this;
    }

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param \CHTTP_Request $request
     * @param string         $channel
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @return mixed
     */
    protected function verifyUserCanAccessChannel($request, $channel) {
        foreach ($this->channels as $pattern => $callback) {
            if (!$this->channelNameMatchesPattern($channel, $pattern)) {
                continue;
            }

            $parameters = $this->extractAuthParameters($pattern, $channel, $callback);

            $handler = $this->normalizeChannelHandlerToCallable($callback);

            if ($result = $handler($this->retrieveUser($request, $channel), ...$parameters)) {
                return $this->validAuthenticationResponse($request, $result);
            }
        }

        throw new AccessDeniedHttpException();
    }

    /**
     * Extract the parameters from the given pattern and channel.
     *
     * @param string          $pattern
     * @param string          $channel
     * @param callable|string $callback
     *
     * @return array
     */
    protected function extractAuthParameters($pattern, $channel, $callback) {
        $callbackParameters = $this->extractParameters($callback);

        return c::collect($this->extractChannelKeys($pattern, $channel))->reject(function ($value, $key) {
            return is_numeric($key);
        })->map(function ($value, $key) use ($callbackParameters) {
            return $this->resolveBinding($key, $value, $callbackParameters);
        })->values()->all();
    }

    /**
     * Extracts the parameters out of what the user passed to handle the channel authentication.
     *
     * @param callable|string $callback
     *
     * @throws \Exception
     *
     * @return \ReflectionParameter[]
     */
    protected function extractParameters($callback) {
        if (is_callable($callback)) {
            return (new ReflectionFunction($callback))->getParameters();
        } elseif (is_string($callback)) {
            return $this->extractParametersFromClass($callback);
        }

        throw new Exception('Given channel handler is an unknown type.');
    }

    /**
     * Extracts the parameters out of a class channel's "join" method.
     *
     * @param string $callback
     *
     * @throws \Exception
     *
     * @return \ReflectionParameter[]
     */
    protected function extractParametersFromClass($callback) {
        $reflection = new ReflectionClass($callback);

        if (!$reflection->hasMethod('join')) {
            throw new Exception('Class based channel must define a "join" method.');
        }

        return $reflection->getMethod('join')->getParameters();
    }

    /**
     * Extract the channel keys from the incoming channel name.
     *
     * @param string $pattern
     * @param string $channel
     *
     * @return array
     */
    protected function extractChannelKeys($pattern, $channel) {
        preg_match('/^' . preg_replace('/\{(.*?)\}/', '(?<$1>[^\.]+)', $pattern) . '/', $channel, $keys);

        return $keys;
    }

    /**
     * Resolve the given parameter binding.
     *
     * @param string $key
     * @param string $value
     * @param array  $callbackParameters
     *
     * @return mixed
     */
    protected function resolveBinding($key, $value, $callbackParameters) {
        $newValue = $this->resolveExplicitBindingIfPossible($key, $value);

        return $newValue === $value ? $this->resolveImplicitBindingIfPossible(
            $key,
            $value,
            $callbackParameters
        ) : $newValue;
    }

    /**
     * Resolve an explicit parameter binding if applicable.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function resolveExplicitBindingIfPossible($key, $value) {
        $binder = $this->binder();

        if ($binder && $binder->getBindingCallback($key)) {
            return call_user_func($binder->getBindingCallback($key), $value);
        }

        return $value;
    }

    /**
     * Resolve an implicit parameter binding if applicable.
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $callbackParameters
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @return mixed
     */
    protected function resolveImplicitBindingIfPossible($key, $value, $callbackParameters) {
        foreach ($callbackParameters as $parameter) {
            if (!$this->isImplicitlyBindable($key, $parameter)) {
                continue;
            }

            $className = CBase_Reflector::getParameterClassName($parameter);

            if (is_null($model = (new $className())->resolveRouteBinding($value))) {
                throw new AccessDeniedHttpException();
            }

            return $model;
        }

        return $value;
    }

    /**
     * Determine if a given key and parameter is implicitly bindable.
     *
     * @param string               $key
     * @param \ReflectionParameter $parameter
     *
     * @return bool
     */
    protected function isImplicitlyBindable($key, $parameter) {
        return $parameter->getName() === $key
            && CBase_Reflector::isParameterSubclassOf($parameter, CRouting_UrlRoutableInterface::class);
    }

    /**
     * Format the channel array into an array of strings.
     *
     * @param array $channels
     *
     * @return array
     */
    protected function formatChannels(array $channels) {
        return array_map(function ($channel) {
            return (string) $channel;
        }, $channels);
    }

    /**
     * Get the model binding registrar instance.
     *
     * @return \CRouting_Contract_BindingRegistrarInterface
     */
    protected function binder() {
        if (!$this->bindingRegistrar) {
            $this->bindingRegistrar = CContainer::getInstance()->bound(CRouting_Contract_BindingRegistrarInterface::class)
                ? CContainer::getInstance()->make(CRouting_Contract_BindingRegistrarInterface::class) : null;
        }

        return $this->bindingRegistrar;
    }

    /**
     * Normalize the given callback into a callable.
     *
     * @param mixed $callback
     *
     * @return callable
     */
    protected function normalizeChannelHandlerToCallable($callback) {
        return is_callable($callback) ? $callback : function (...$args) use ($callback) {
            return CContainer::getInstance()
                ->make($callback)
                ->join(...$args);
        };
    }

    /**
     * Retrieve the authenticated user using the configured guard (if any).
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $channel
     *
     * @return mixed
     */
    protected function retrieveUser($request, $channel) {
        $options = $this->retrieveChannelOptions($channel);

        $guards = $options['guards'] ?? null;

        if (is_null($guards)) {
            return $request->user();
        }

        foreach (carr::wrap($guards) as $guard) {
            if ($user = $request->user($guard)) {
                return $user;
            }
        }
    }

    /**
     * Retrieve options for a certain channel.
     *
     * @param string $channel
     *
     * @return array
     */
    protected function retrieveChannelOptions($channel) {
        foreach ($this->channelOptions as $pattern => $options) {
            if (!$this->channelNameMatchesPattern($channel, $pattern)) {
                continue;
            }

            return $options;
        }

        return [];
    }

    /**
     * Check if the channel name from the request matches a pattern from registered channels.
     *
     * @param string $channel
     * @param string $pattern
     *
     * @return bool
     */
    protected function channelNameMatchesPattern($channel, $pattern) {
        return cstr::is(preg_replace('/\{(.*?)\}/', '*', $pattern), $channel);
    }
}
