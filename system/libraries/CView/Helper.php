<?php

/**
 * Description of Helper
 *
 * @author Hery
 */
class CView_Helper {

    /**
     * Hint path delimiter value.
     *
     * @var string
     */
    const HINT_PATH_DELIMITER = '::';

    /**
     * Normalize the given view name.
     *
     * @param  string  $name
     * @return string
     */
    public static function normalize($name) {
        $delimiter = static::HINT_PATH_DELIMITER;

        if (strpos($name, $delimiter) === false) {
            return str_replace('/', '.', $name);
        }

        list($namespace, $name) = explode($delimiter, $name);

        return $namespace . $delimiter . str_replace('/', '.', $name);
    }

}
