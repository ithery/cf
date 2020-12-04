<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Common helper class.
 */
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class c {

    /**
     * 
     * @param string $str
     * @return string
     */
    public static function fixPath($str) {
        $str = str_replace(['/', '\\'], DS, $str);
        return rtrim($str, DS) . DS;
    }

    public static function urShift($a, $b) {
        if ($b == 0) {
            return $a;
        }
        return ($a >> $b) & ~(1 << (8 * PHP_INT_SIZE - 1) >> ($b - 1));
    }

    public static function manimgurl($path) {
        return curl::base() . "public/manual/" . $path;
    }

    public static function baseIteratee($value) {
        if (\is_callable($value)) {
            return $value;
        }
        if (null === $value) {
            return array('c', 'identity');
        }
        if (\is_array($value)) {
            return 2 === \count($value) && [0, 1] === \array_keys($value) ? static::baseMatchesProperty($value[0], $value[1]) : static::baseMatches($value);
        }
        return static::property($value);
    }

    public static function baseMatchesProperty($property, $source) {
        return function ($value, $index, $collection) use ($property, $source) {
            $propertyVal = static::property($property);
            return static::isEqual($propertyVal($value, $index, $collection), $source);
        };
    }

    public static function baseMatches($source) {
        return function ($value, $index, $collection) use ($source) {
            if ($value === $source || static::isEqual($value, $source)) {
                return true;
            }
            if (\is_array($source) || $source instanceof \Traversable) {
                foreach ($source as $k => $v) {
                    $propK = c::property($k);
                    if (!static::isEqual($propK($value, $index, $collection), $v)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        };
    }

    public static function isEqual($value, $other) {
        $factory = CComparator::createFactory();
        $comparator = $factory->getComparatorFor($value, $other);
        try {
            $comparator->assertEquals($value, $other);
            return true;
        } catch (CComparator_Exception_ComparisonFailureException $failure) {
            return false;
        }
    }

    /**
     * Creates a function that returns the value at `path` of a given object.
     *
     * @param array|string $path The path of the property to get.
     *
     * @return callable Returns the new accessor function.
     * @example
     * <code>
     * $objects = [
     *   [ 'a' => [ 'b' => 2 ] ],
     *   [ 'a' => [ 'b' => 1 ] ]
     * ];
     *
     * carr::map($objects, property('a.b'));
     * // => [2, 1]
     *
     * carr::map(sortBy($objects, property(['a', 'b'])), 'a.b');
     * // => [1, 2]
     * </code>
     */
    public static function property($path) {
        $propertyAccess = PropertyAccess::createPropertyAccessorBuilder()
                ->disableExceptionOnInvalidIndex()
                ->getPropertyAccessor();
        return function ($value, $index = 0, $collection = []) use ($path, $propertyAccess) {
            $path = \implode('.', (array) $path);
            if (\is_array($value)) {

                if (false !== \strpos($path, '.')) {
                    $paths = \explode('.', $path);
                    foreach ($paths as $path) {
                        $propPath = static::property($path);
                        $value = $propPath($value, $index, $collection);
                    }
                    return $value;
                }

                if (\is_string($path) && $path[0] !== '[' && $path[strlen($path) - 1] !== ']') {
                    $path = "[$path]";
                }
            }
            try {
                return $propertyAccess->getValue($value, $path);
            } catch (NoSuchPropertyException $e) {
                return null;
            } catch (NoSuchIndexException $e) {
                return null;
            }
        };
    }

    public static function baseGet($object, $path, $defaultValue = null) {
        $path = static::castPath($path, $object);
        $index = 0;
        $length = \count($path);
        while ($object !== null && !is_scalar($object) && $index < $length) {
            $property = static::property(static::toKey($path[$index++]));
            $object = $property($object);
        }
        return ($index > 0 && $index === $length) ? $object : $defaultValue;
    }

    /**
     * Converts `value` to a string key if it's not a string.
     *
     * @param mixed $value The value to inspect.
     *
     * @return string Returns the key.
     */
    public static function toKey($value) {
        if (\is_string($value)) {
            return $value;
        }
        $result = (string) $value;
        return ('0' === $result && (1 / $value) === -INF) ? '-0' : $result;
    }

    public static function castPath($value, $object) {
        if (\is_array($value)) {
            return $value;
        }
        return static::isKey($value, $object) ? [$value] : static::stringToPath((string) $value);
    }

    /**
     * Checks if `value` is a property name and not a property path.
     *
     * @param mixed        $value  The value to check.
     * @param object|array $object The object to query keys on.
     *
     * @return boolean Returns `true` if `value` is a property name, else `false`.
     */
    public static function isKey($value, $object = []) {

        /** Used to match property names within property paths. */
        $reIsDeepProp = '#\.|\[(?:[^[\]]*|(["\'])(?:(?!\1)[^\\\\]|\\.)*?\1)\]#';
        $reIsPlainProp = '/^\w*$/';
        if (\is_array($value)) {
            return false;
        }
        if (\is_numeric($value)) {
            return true;
        }
        $forceObject = ((object) $object);
        return \preg_match($reIsPlainProp, $value) || !\preg_match($reIsDeepProp, $value) || (null !== $object && isset($forceObject->$value));
    }

    public static function stringToPath(...$args) {
        $memoizeCapped = static::memoizeCapped(function ($string) {

                    $reLeadingDot = '/^\./';
                    $rePropName = '#[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["\'])((?:(?!\2)[^\\\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|$))#';
                    /** Used to match backslashes in property paths. */
                    $reEscapeChar = '/\\(\\)?/g';
                    $result = [];
                    if (\preg_match($reLeadingDot, $string)) {
                        $result[] = '';
                    }
                    \preg_match_all($rePropName, $string, $matches, PREG_SPLIT_DELIM_CAPTURE);
                    foreach ($matches as $match) {
                        $result[] = isset($match[1]) ? $match[1] : $match[0];
                    }
                    return $result;
                });
        return $memoizeCapped(...$args);
    }

    public static function memoizeCapped(callable $func) {
        $MaxMemoizeSize = 500;
        $result = static::memoize($func, function ($key) use ($MaxMemoizeSize) {
                    if ($this->cache->getSize() === $MaxMemoizeSize) {
                        $this->cache->clear();
                    }
                    return $key;
                });
        return $result;
    }

    /**
     * Creates a function that memoizes the result of `func`. If `resolver` is
     * provided, it determines the cache key for storing the result based on the
     * arguments provided to the memoized function. By default, the first argument
     * provided to the memoized function is used as the map cache key
     *
     * **Note:** The cache is exposed as the `cache` property on the memoized
     * function. Its creation may be customized by replacing the `_.memoize.Cache`
     * constructor with one whose instances implement the
     * [`Map`](http://ecma-international.org/ecma-262/7.0/#sec-properties-of-the-map-prototype-object)
     * method interface of `clear`, `delete`, `get`, `has`, and `set`.
     *
     * @category Function
     *
     * @param callable      $func     The function to have its output memoized.
     * @param callable|null $resolver The function to resolve the cache key.
     *
     * @return callable Returns the new memoized function.
     *
     * @example
     * <code>
     * $object = ['a' => 1, 'b' => 2];
     * $other = ['c' => 3, 'd' => 4];
     *
     * $values = c::memoize('c::values');
     * $values($object);
     * // => [1, 2]
     *
     * $values($other);
     * // => [3, 4]
     *
     * $object['a'] = 2;
     * $values($object);
     * // => [1, 2]
     *
     * // Modify the result cache.
     * $values->cache->set($object, ['a', 'b']);
     * $values($object);
     * // => ['a', 'b']
     * </code>
     */
    public static function memoize(callable $func, callable $resolver = null) {
        $memoized = CBase::createMemoizeResolver($func, $resolver);
        $memoized->cache = CBase::createMapCache();
        return $memoized;
    }

    /**
     * Gets the value at path of object. If the resolved value is null the defaultValue is returned in its place.
     *
     * @category Object
     *
     * @param mixed $object The associative array or object to fetch value from
     * @param array|string $path Dot separated or array of string
     * @param mixed $defaultValue (optional)The value returned for unresolved or null values.
     *
     * @return mixed Returns the resolved value.
     *
     * @author punit-kulal
     *
     * @example
     * <code>
     * $sampleArray = ["key1" => ["key2" => ["key3" => "val1", "key4" => ""]]];
     * get($sampleArray, 'key1.key2.key3');
     * // => "val1"
     *
     * get($sampleArray, 'key1.key2.key5', "default");
     * // => "default"
     *
     * get($sampleArray, 'key1.key2.key4', "default");
     * // => ""
     * </code>
     */
    public static function get($object, $path, $defaultValue = null) {

        return ($object !== null ? c::baseGet($object, $path, $defaultValue) : $defaultValue);
    }

    public static function assocIndexOf(array $array, $key) {
        $length = \count($array);
        while ($length--) {
            if (static::eq($array[$length][0], $key)) {
                return $length;
            }
        }
        return -1;
    }

    /**
     * Performs a comparison between two values to determine if they are equivalent.
     *
     * @param mixed $value The value to compare.
     * @param mixed $other The other value to compare.
     *
     * @return boolean Returns `true` if the values are equivalent, else `false`.
     * @example
     * <code>
     * $object = (object) ['a' => 1];
     * $other = (object) ['a' => 1];
     *
     * eq($object, $object);
     * // => true
     *
     * eq($object, $other);
     * // => false
     *
     * eq('a', 'a');
     * // => true
     *
     * eq(['a'], (object) ['a']);
     * // => false
     *
     * eq(INF, INF);
     * // => true
     * </code>
     */
    public static function eq($value, $other) {
        return $value === $other;
    }

    /**
     * Create a collection from the given value.
     *
     * @param  mixed  $value
     * @return CCollection
     */
    public static function collect($value = null) {
        return new CCollection($value);
    }

    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @return mixed
     */
    public static function tap($value, $callback = null) {
        if (is_null($callback)) {
            return new CBase_HigherOrderTapProxy($value);
        }

        $callback($value);

        return $value;
    }

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    public static function classBasename($class) {
        $class = is_object($class) ? get_class($class) : $class;

        $basename = basename(str_replace('\\', '/', $class));
        $basename = carr::last(explode("_", $basename));
        return $basename;
    }

    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param  string  $trait
     * @return array
     */
    public static function traitUsesRecursive($trait) {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += self::traitUsesRecursive($trait);
        }

        return $traits;
    }

    /**
     * Returns all traits used by a class, its subclasses and trait of their traits.
     *
     * @param  object|string  $class
     * @return array
     */
    public static function classUsesRecursive($class) {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_merge([$class => $class], class_parents($class)) as $class) {
            $results += self::traitUsesRecursive($class);
        }

        return array_unique($results);
    }

    /**
     * Returns true of traits is used by a class, its subclasses and trait of their traits.
     *
     * @param  object|string  $class
     * @param  string  $trait
     * @return array
     */
    public static function hasTrait($class, $trait) {
        return in_array($trait, static::classUsesRecursive($class));
    }

    /**
     * Catch a potential exception and return a default value.
     *
     * @param  callable  $callback
     * @param  mixed  $rescue
     * @param  bool  $report
     * @return mixed
     */
    public static function rescue(callable $callback, $rescue = null, $report = true) {
        try {
            return $callback();
        } catch (Throwable $e) {
            if ($report) {
                static::report($e);
            }

            return static::value($rescue);
        }
    }

    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @return mixed
     */
    public static function with($value, callable $callback = null) {
        return is_null($callback) ? $value : $callback($value);
    }

    /**
     * Report an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public static function report($exception) {
        if ($exception instanceof Throwable &&
                !$exception instanceof Exception) {
            $exception = new FatalThrowableError($exception);
        }
        $exceptionHandler = CException::exceptionHandler();
        $exceptionHandler->report($exception);
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function value($value) {
        return $value instanceof Closure ? $value() : $value;
    }

    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public static function event(...$args) {
        return CEvent::dispatch(...$args);
    }

    /**
     * Create a new Carbon instance for the current time.
     *
     * @param  \DateTimeZone|string|null  $tz
     * @return CCarbon
     */
    public static function now($tz = null) {
        return CCarbon::now($tz);
    }

    public static function hrtime($getAsNumber = false) {
        if (function_exists('hrtime')) {
            return hrtime($getAsNumber);
        }

        if ($getAsNumber) {
            return microtime(true) * 1e+6;
        }
        $mt = microtime();
        $s = floor($mt);
        return [$s, ($mt - $s) * 1e+6];
    }

    public static function html($str) {
        return chtml::specialchars($str);
    }

    public static function dirname($path, $count = 1) {
        if ($count > 1) {
            return dirname(static::dirname($path, --$count));
        } else {
            return dirname($path);
        }
    }

    /**
     * Provide access to optional objects.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @return mixed
     */
    public static function optional($value = null, callable $callback = null) {
        if (is_null($callback)) {
            return new COptional($value);
        } elseif (!is_null($value)) {
            return $callback($value);
        }
    }

    /**
     * Encode HTML special characters in a string.
     *
     * @param  CBase_DeferringDisplayableValue|CInterface_Htmlable|string  $value
     * @param  bool  $doubleEncode
     * @return string
     */
    public static function e($value, $doubleEncode = true) {
        if ($value instanceof CBase_DeferringDisplayableValue) {
            $value = $value->resolveDisplayableValue();
        }

        if ($value instanceof CInterface_Htmlable) {
            return $value->toHtml();
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }

    /**
     * 
     * @param string $string
     * @return \cstr|CBase_String
     */
    public static function str($string = null) {
        if (is_null($string)) {
            return new CBase_ForwarderStaticClass(cstr::class);
        };

        return cstr::of($string);
    }

    /**
     * Throw the given exception unless the given condition is true.
     *
     * @param  mixed  $condition
     * @param  \Throwable|string  $exception
     * @param  array  ...$parameters
     * @return mixed
     *
     * @throws \Throwable
     */
    public static function throwUnless($condition, $exception, ...$parameters) {
        if (!$condition) {
            throw (is_string($exception) ? new $exception(...$parameters) : $exception);
        }

        return $condition;
    }

    /**
     * Throw the given exception if the given condition is true.
     *
     * @param  mixed  $condition
     * @param  \Throwable|string  $exception
     * @param  array  ...$parameters
     * @return mixed
     *
     * @throws \Throwable
     */
    public static function throwIf($condition, $exception, ...$parameters) {
        if ($condition) {
            throw (is_string($exception) ? new $exception(...$parameters) : $exception);
        }

        return $condition;
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function env($key, $default = null) {
        return CEnv::get($key, $default);
    }

    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return CTranslation_Translator|string|array|null
     */
    public static function trans($key = null, $replace = [], $locale = null) {
        return CF::lang($key, $replace, $locale);
    }

    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string|array|null
     */
    function __($key = null, $replace = [], $locale = null) {
        if (is_null($key)) {
            return $key;
        }

        return static::trans($key, $replace, $locale);
    }

    /**
     * @return CSession
     */
    public static function session() {
        return CSession::instance();
    }

    /**
     * Generate a url for the application.
     *
     * @param  string|null  $path
     * @param  mixed  $parameters
     * @param  bool|null  $secure
     * @return CRouting_UrlGenerator|string
     */
    public static function url($path = null, $parameters = [], $secure = null) {
        if (is_null($path)) {
            return CRouting::urlGenerator();
        }

        return CRouting::urlGenerator()->to($path, $parameters, $secure);
    }

    /**
     * @return CStorage
     */
    public static function storage() {
        return CStorage::instance();
    }

    /**
     * Create a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return CValidation_Validator|CValidation_Factory
     */
    public static function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = []) {
        $factory = CValidation::factory();

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data, $rules, $messages, $customAttributes);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  CInterface_Arrayable|array  $data
     * @param  array  $mergeData
     * @return CView_View|CView_Factory
     */
    function view($view = null, $data = [], $mergeData = []) {
        $factory = CView::factory();

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }

    /**
     * Throw an HttpException with the given data unless the given condition is true.
     *
     * @param  bool  $boolean
     * @param  CHTTP_Response|\CInterface_Responsable|int  $code
     * @param  string  $message
     * @param  array  $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public static function abortUnless($boolean, $code, $message = '', array $headers = []) {
        if (!$boolean) {
            static::abort($code, $message, $headers);
        }
    }

    /**
     * Displays a 404 page.
     *
     * @throws  C_404_Exception
     * @param   string  URI of page
     * @param   string  custom template
     * @return  void
     */
    public static function show404($page = FALSE, $template = FALSE) {
        return CF::abort(404);
    }

    public static function abort($code, $message = '', array $headers = []) {
        if ($code instanceof CHTTP_Response) {
            throw new CHttp_Exception_ResponseException($code);
        } elseif ($code instanceof CInterface_Responsable) {
            throw new CHttp_Exception_ResponseException($code->toResponse(CHTTP::request()));
        }

        if ($code == 404) {
            throw new CHTTP_Exception_NotFoundHttpException($message);
        }

        throw new CHTTP_Exception_HttpException($code, $message, null, $headers);
    }

    /**
     * Throw an HttpException with the given data if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  CHTTP_Response|\CInterface_Responsable|int  $code
     * @param  string  $message
     * @param  array  $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public static function abortIf($boolean, $code, $message = '', array $headers = []) {
        if ($boolean) {
            static::abort($code, $message, $headers);
        }
    }

    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return CHTTP_Request|string|array
     */
    function request($key = null, $default = null) {
        if (is_null($key)) {
            return CHTTP::request();
        }

        if (is_array($key)) {
            return CHTTP::request()->only($key);
        }

        $value = CHTTP::request()->__get($key);

        return is_null($value) ? c::value($default) : $value;
    }

    /**
     * Return a new response from the application.
     *
     * @param  CView|string|array|null  $content
     * @param  int  $status
     * @param  array  $headers
     * @return CHTTP_Response|CHTTP_ResponseFactory
     */
    public static function response($content = '', $status = 200, array $headers = []) {
        $factory = CHTTP::responseFactory();

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }

    /**
     * Determine if the given value is "blank".
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function blank($value) {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }

    /**
     * Determine if a value is "filled".
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function filled($value) {
        return !static::blank($value);
    }

    /**
     * Get an instance of the redirector.
     *
     * @param  string|null  $to
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return CHTTP_Redirector|CHttp_RedirectResponse
     */
    public static function redirect($to = null, $status = 302, $headers = [], $secure = null) {
        if (is_null($to)) {
            return CHTTP::redirector();
        }

        return CHTTP::redirector()->to($to, $status, $headers, $secure);
    }

}

// End c