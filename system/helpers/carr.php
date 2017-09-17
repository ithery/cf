<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Array helper class.
 *
 * $Id: arr.php 4346 2009-05-11 17:08:15Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class carr {

    /**
     * Tests if an array is associative or not.
     *
     *     // Returns TRUE
     *     carr::is_assoc(array('username' => 'john.doe'));
     *
     *     // Returns FALSE
     *     carr::is_assoc('foo', 'bar');
     *
     * @param   array   $array  array to check
     * @return  boolean
     */
    public static function is_assoc(array $array) {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

    /**
     * Test if a value is an array with an additional check for array-like objects.
     *
     *     // Returns TRUE
     *     carr::is_array(array());
     *     carr::is_array(new ArrayObject);
     *
     *     // Returns FALSE
     *     carr::is_array(FALSE);
     *     carr::is_array('not an array!');
     *     carr::is_array(Database::instance());
     *
     * @param   mixed   $value  value to check
     * @return  boolean
     */
    public static function is_array($value) {
        if (is_array($value)) {
            // Definitely an array
            return TRUE;
        } else {
            // Possibly a Traversable object, functionally the same as an array
            return (is_object($value) AND $value instanceof Traversable);
        }
    }

    /**
     * Retrieve a single key from an array. If the key does not exist in the
     * array, the default value will be returned instead.
     *
     *     // Get the value "username" from $_POST, if it exists
     *     $username = carr::get($_POST, 'username');
     *
     *     // Get the value "sorting" from $_GET, if it exists
     *     $sorting = carr::get($_GET, 'sorting');
     *
     * @param   array   $array      array to extract from
     * @param   string  $key        key name
     * @param   mixed   $default    default value
     * @return  mixed
     */
    public static function get($array, $key, $default = NULL) {
        if ($array instanceof ArrayObject) {
            // This is a workaround for inconsistent implementation of isset between PHP and HHVM
            // See https://github.com/facebook/hhvm/issues/3437
            return $array->offsetExists($key) ? $array->offsetGet($key) : $default;
        } else {
            return isset($array[$key]) ? $array[$key] : $default;
        }
    }

    /**
     * Gets a value from an array using a dot separated path.
     *
     *     // Get the value of $array['foo']['bar']
     *     $value = carr::path($array, 'foo.bar');
     *
     * Using a wildcard "*" will search intermediate arrays and return an array.
     *
     *     // Get the values of "color" in theme
     *     $colors = carr::path($array, 'theme.*.color');
     *
     *     // Using an array of keys
     *     $colors = carr::path($array, array('theme', '*', 'color'));
     *
     * @param   array   $array      array to search
     * @param   mixed   $path       key path string (delimiter separated) or array of keys
     * @param   mixed   $default    default value if the path is not set
     * @param   string  $delimiter  key path delimiter
     * @return  mixed
     */
    public static function path($array, $path, $default = NULL, $delimiter = NULL) {
        if (!carr::is_array($array)) {
            // This is not an array!
            return $default;
        }

        if (is_array($path)) {
            // The path has already been separated into keys
            $keys = $path;
        } else {
            if (array_key_exists($path, $array)) {
                // No need to do extra processing
                return $array[$path];
            }

            if ($delimiter === NULL) {
                // Use the default delimiter .
                $delimiter = '.';
            }

            // Remove starting delimiters and spaces
            $path = ltrim($path, "{$delimiter} ");

            // Remove ending delimiters, spaces, and wildcards
            $path = rtrim($path, "{$delimiter} *");

            // Split the keys by delimiter
            $keys = explode($delimiter, $path);
        }

        do {
            $key = array_shift($keys);

            if (ctype_digit($key)) {
                // Make the key an integer
                $key = (int) $key;
            }

            if (isset($array[$key])) {
                if ($keys) {
                    if (carr::is_array($array[$key])) {
                        // Dig down into the next part of the path
                        $array = $array[$key];
                    } else {
                        // Unable to dig deeper
                        break;
                    }
                } else {
                    // Found the path requested
                    return $array[$key];
                }
            } elseif ($key === '*') {
                // Handle wildcards

                $values = array();
                foreach ($array as $arr) {
                    if ($value = carr::path($arr, implode('.', $keys))) {
                        $values[] = $value;
                    }
                }

                if ($values) {
                    // Found the values requested
                    return $values;
                } else {
                    // Unable to dig deeper
                    break;
                }
            } else {
                // Unable to dig deeper
                break;
            }
        } while ($keys);

        // Unable to find the value requested
        return $default;
    }

    /**
     * Set a value on an array by path.
     *
     * @see carr::path()
     * @param array   $array     Array to update
     * @param string  $path      Path
     * @param mixed   $value     Value to set
     * @param string  $delimiter Path delimiter
     */
    public static function set_path(& $array, $path, $value, $delimiter = NULL) {
        if (!$delimiter) {
            // Use the default delimiter
            $delimiter = '.';
        }

        // The path has already been separated into keys
        $keys = $path;
        if (!carr::is_array($path)) {
            // Split the keys by delimiter
            $keys = explode($delimiter, $path);
        }

        // Set current $array to inner-most array path
        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (ctype_digit($key)) {
                // Make the key an integer
                $key = (int) $key;
            }

            if (!isset($array[$key])) {
                $array[$key] = array();
            }

            $array = & $array[$key];
        }

        // Set key on inner-most array
        $array[array_shift($keys)] = $value;
    }

    /**
     * Return a callback array from a string, eg: limit[10,20] would become
     * array('limit', array('10', '20'))
     *
     * @param   string  callback string
     * @return  array
     */
    public static function callback_string($str) {
        // command[param,param]
        if (preg_match('/([^\[]*+)\[(.+)\]/', (string) $str, $match)) {
            // command
            $command = $match[1];

            // param,param
            $params = preg_split('/(?<!\\\\),/', $match[2]);
            $params = str_replace('\,', ',', $params);
        } else {
            // command
            $command = $str;

            // No params
            $params = NULL;
        }

        return array($command, $params);
    }

    /**
     * Rotates a 2D array clockwise.
     * Example, turns a 2x3 array into a 3x2 array.
     *
     * @param   array    array to rotate
     * @param   boolean  keep the keys in the final rotated array. the sub arrays of the source array need to have the same key values.
     *                   if your subkeys might not match, you need to pass FALSE here!
     * @return  array
     */
    public static function rotate($source_array, $keep_keys = TRUE) {
        $new_array = array();
        foreach ($source_array as $key => $value) {
            $value = ($keep_keys === TRUE) ? $value : array_values($value);
            foreach ($value as $k => $v) {
                $new_array[$k][$key] = $v;
            }
        }

        return $new_array;
    }

    /**
     * Removes a key from an array and returns the value.
     *
     * @param   string  key to return
     * @param   array   array to work on
     * @return  mixed   value of the requested array key
     */
    public static function remove($key, & $array) {
        if (!array_key_exists($key, $array))
            return NULL;

        $val = $array[$key];
        unset($array[$key]);

        return $val;
    }

    /**
     * Retrieves multiple paths from an array. If the path does not exist in the
     * array, the default value will be added instead.
     *
     *     // Get the values "username", "password" from $_POST
     *     $auth = Arr::extract($_POST, array('username', 'password'));
     *
     *     // Get the value "level1.level2a" from $data
     *     $data = array('level1' => array('level2a' => 'value 1', 'level2b' => 'value 2'));
     *     Arr::extract($data, array('level1.level2a', 'password'));
     *
     * @param   array  $array    array to extract paths from
     * @param   array  $paths    list of path
     * @param   mixed  $default  default value
     * @return  array
     */
    public static function extract($array, array $paths, $default = NULL) {
        $found = array();
        foreach ($paths as $path) {
            carr::set_path($found, $path, carr::path($array, $path, $default));
        }

        return $found;
    }

    /**
     * Recursively merge two or more arrays. Values in an associative array
     * overwrite previous values with the same key. Values in an indexed array
     * are appended, but only when they do not already exist in the result.
     *
     * Note that this does not work the same as [array_merge_recursive](http://php.net/array_merge_recursive)!
     *
     *     $john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
     *     $mary = array('name' => 'mary', 'children' => array('jane'));
     *
     *     // John and Mary are married, merge them together
     *     $john = Arr::merge($john, $mary);
     *
     *     // The output of $john will now be:
     *     array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane'))
     *
     * @param   array  $array1      initial array
     * @param   array  $array2,...  array to merge
     * @return  array
     */
    public static function merge($array1, $array2) {
        if (carr::is_assoc($array2)) {
            foreach ($array2 as $key => $value) {
                if (is_array($value)
                        AND isset($array1[$key])
                        AND is_array($array1[$key])
                ) {
                    $array1[$key] = carr::merge($array1[$key], $value);
                } else {
                    $array1[$key] = $value;
                }
            }
        } else {
            foreach ($array2 as $value) {
                if (!in_array($value, $array1, TRUE)) {
                    $array1[] = $value;
                }
            }
        }

        if (func_num_args() > 2) {
            foreach (array_slice(func_get_args(), 2) as $array2) {
                if (Arr::is_assoc($array2)) {
                    foreach ($array2 as $key => $value) {
                        if (is_array($value)
                                AND isset($array1[$key])
                                AND is_array($array1[$key])
                        ) {
                            $array1[$key] = Arr::merge($array1[$key], $value);
                        } else {
                            $array1[$key] = $value;
                        }
                    }
                } else {
                    foreach ($array2 as $value) {
                        if (!in_array($value, $array1, TRUE)) {
                            $array1[] = $value;
                        }
                    }
                }
            }
        }

        return $array1;
    }

    /**
     * Overwrites an array with values from input arrays.
     * Keys that do not exist in the first array will not be added!
     *
     *     $a1 = array('name' => 'john', 'mood' => 'happy', 'food' => 'bacon');
     *     $a2 = array('name' => 'jack', 'food' => 'tacos', 'drink' => 'beer');
     *
     *     // Overwrite the values of $a1 with $a2
     *     $array = Arr::overwrite($a1, $a2);
     *
     *     // The output of $array will now be:
     *     array('name' => 'jack', 'mood' => 'happy', 'food' => 'tacos')
     *
     * @param   array   $array1 master array
     * @param   array   $array2 input arrays that will overwrite existing values
     * @return  array
     */
    public static function overwrite($array1, $array2) {
        foreach (array_intersect_key($array2, $array1) as $key => $value) {
            $array1[$key] = $value;
        }

        if (func_num_args() > 2) {
            foreach (array_slice(func_get_args(), 2) as $array2) {
                foreach (array_intersect_key($array2, $array1) as $key => $value) {
                    $array1[$key] = $value;
                }
            }
        }

        return $array1;
    }

    /**
     * Because PHP does not have this function.
     *
     * @param   array   array to unshift
     * @param   string  key to unshift
     * @param   mixed   value to unshift
     * @return  array
     */
    public static function unshift_assoc(array & $array, $key, $val) {
        $array = array_reverse($array, TRUE);
        $array[$key] = $val;
        $array = array_reverse($array, TRUE);

        return $array;
    }

    /**
     * Because PHP does not have this function, and array_walk_recursive creates
     * references in arrays and is not truly recursive.
     *
     * @param   mixed  callback to apply to each member of the array
     * @param   array  array to map to
     * @return  array
     */
    public static function map_recursive($callback, array $array) {
        foreach ($array as $key => $val) {
            // Map the callback to the key
            $array[$key] = is_array($val) ? carr::map_recursive($callback, $val) : call_user_func($callback, $val);
        }

        return $array;
    }

    /**
     * @param mixed $needle     the value to search for
     * @param array $haystack   an array of values to search in
     * @param boolean $sort     sort the array now
     * @return integer|FALSE    the index of the match or FALSE when not found
     */
    public static function binary_search($needle, $haystack, $sort = FALSE) {
        if ($sort) {
            sort($haystack);
        }

        $high = count($haystack) - 1;
        $low = 0;

        while ($low <= $high) {
            $mid = ($low + $high) >> 1;

            if ($haystack[$mid] < $needle) {
                $low = $mid + 1;
            } elseif ($haystack[$mid] > $needle) {
                $high = $mid - 1;
            } else {
                return $mid;
            }
        }

        return FALSE;
    }

    /**
     * Fill an array with a range of numbers.
     *
     * @param   integer  stepping
     * @param   integer  ending number
     * @return  array
     */
    public static function range($step = 10, $max = 100) {
        if ($step < 1)
            return array();

        $array = array();
        for ($i = $step; $i <= $max; $i += $step) {
            $array[$i] = $i;
        }

        return $array;
    }

    /**
     * Recursively convert an array to an object.
     *
     * @param   array   array to convert
     * @return  object
     */
    public static function to_object(array $array, $class = 'stdClass') {
        $object = new $class;

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Convert the array to an object
                $value = carr::to_object($value, $class);
            }

            // Add the value to the object
            $object->{$key} = $value;
        }

        return $object;
    }

    public static function replace() {
        $args = func_get_args();
        $num_args = func_num_args();
        $res = array();
        for ($i = 0; $i < $num_args; $i++) {
            if (is_array($args[$i])) {
                foreach ($args[$i] as $key => $val) {
                    $res[$key] = $val;
                }
            } else {
                trigger_error(__FUNCTION__ . '(): Argument #' . ($i + 1) . ' is not an array', E_USER_WARNING);
                return NULL;
            }
        }
        return $res;
    }

}

// End arr
