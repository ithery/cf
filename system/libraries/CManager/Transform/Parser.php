<?php

class CManager_Transform_Parser {
    /**
     * Normalizes a legacy method so that we can accept for new method name only.
     *
     * @param string $method
     *
     * @return string
     */
    public static function normalizeMethod($method) {
        $methodMap = [
            'formatBytes' => 'formatByte',
            'format_date' => 'formatDate',
            'short_date_format' => 'formatDate',
            'date_formatted' => 'formatDate',
            'long_date_formatted' => 'formatDatetime',
            'unformat_date' => 'unformatDate',
            'format_long_date' => 'formatDatetime',
            'format_datetime' => 'formatDatetime',
            'unformat_long_date' => 'unformatDatetime',
            'thousand_separator' => 'thousandSeparator',
            'month_name' => 'monthName',
            'html_specialchars' => 'htmlSpecialChars',
            'format_currency' => 'formatCurrency',
            'unformat_currency' => 'unformatCurrency',

        ];

        $method = carr::get($methodMap, $method, $method);

        return cstr::studly(trim($method));
    }

    public static function explodeMethods($methods) {
        $methods = carr::wrap($methods);
        $exploded = [];
        foreach ($methods as $method) {
            $exploded = array_merge($exploded, static::explodeMethod($method));
        }

        return $exploded;
    }

    public static function explodeMethod($method) {
        if (is_string($method)) {
            return explode('|', $method);
        } elseif (is_object($method)) {
            return [static::prepareMethod($method)];
        }

        return array_map([static::class, 'prepareMethod'], $method);
    }

    /**
     * Prepare the given rule for the Validator.
     *
     * @param mixed $method
     *
     * @return mixed
     */
    protected function prepareMethod($method) {
        if ($method instanceof Closure || $method instanceof \Opis\Closure\SerializableClosure) {
            $method = new CManager_Transform_Method_ClosureMethod($method);
        }

        if (!is_object($method)
            || $method instanceof CManager_Transform_Contract_TransformMethodInterface
        ) {
            return $method;
        }

        return (string) $method;
    }

    /**
     * Parse a parameter list.
     *
     * @param string $method
     * @param string $parameter
     *
     * @return array
     */
    protected static function parseParameters($method, $parameter) {
        $method = strtolower($method);

        if (in_array($method, ['regex', 'not_regex', 'notregex'], true)) {
            return [$parameter];
        }

        return str_getcsv($parameter);
    }

    /**
     * Parse an array based method.
     *
     * @param array $rules
     *
     * @return array
     */
    protected static function parseArrayMethod(array $method) {
        return [static::normalizeMethod(carr::get($method, 0, '')), array_slice($method, 1)];
    }

    /**
     * Parse a string based method.
     *
     * @param string $method
     *
     * @return array
     */
    protected static function parseStringMethod($method) {
        $parameters = [];

        if (strpos($method, ':') !== false) {
            list($method, $parameter) = explode(':', $method, 2);

            $parameters = static::parseParameters($method, $parameter);
        }

        return [static::normalizeMethod($method), $parameters];
    }

    /**
     * Extract the methods name and parameters from a method.
     *
     * @param array|string $methods
     *
     * @return array
     */
    public static function parse($methods) {
        if (is_array($methods)) {
            $methods = static::parseArrayMethod($methods);
        } else {
            $methods = static::parseStringMethod($methods);
        }

        return $methods;
    }

    public static function getArguments(array $parameters, $data) {
        if ($data instanceof CModel || $data instanceof CInterface_Arrayable) {
            $data = $data->toArray();
        }
        foreach ($parameters as $index => $parameter) {
            $value = $parameter;
            preg_match_all("/{([\w]*)}/", $value, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $str = $match[1]; //matches str without bracket {}
                $bStr = $match[0]; //matches str with bracket {}
                if (isset($data[$str])) {
                    $value = str_replace($bStr, $data[$str], $value);
                }
            }
            $parameters[$index] = $value;
        }

        return $parameters;
    }
}
