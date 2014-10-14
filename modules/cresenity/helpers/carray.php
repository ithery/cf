<?php

class carray {

    /**
     * Returns the first element in an array
     *
     * @param   array  $array  The array
     * @return  mixed
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function array_first(array $array) {
        return reset($array);
    }

    /**
     * Returns the last element in an array
     *
     * @param   array  $array  The array
     * @return  mixed
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function array_last(array $array) {
        return end($array);
    }

    /**
     * Returns the first key in an array
     *
     * @param   array  $array  The array
     * @return  int|string
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function array_first_key(array $array) {
        reset($array);

        return key($array);
    }

    /**
     * Returns the last key in an array
     *
     * @param   array  $array  The array
     * @return  int|string
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function array_last_key(array $array) {
        end($array);

        return key($array);
    }

    /**
     * Flattens a potentially multi-dimensional array into a one
     * dimensional array
     *
     * @param   array  $array         The array to flatten
     * @param   bool   preserve_keys  Whether or not to preserve array
     *                                keys. Keys from deeply nested arrays
     *                                will overwrite keys from shallowy
     *                                nested arrays
     * @return  array
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function array_flatten(array $array, $preserve_keys = TRUE) {
        $flattened = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattened = array_merge($flattened, self::array_flatten($value, $preserve_keys));
            } else {
                if ($preserve_keys) {
                    $flattened[$key] = $value;
                } else {
                    $flattened[] = $value;
                }
            }
        }

        return $flattened;
    }

    /**
     * Accepts an array, and returns an array of values from that array as
     * specified by $field. For example, if the array is full of objects
     * and you call util::array_pluck( $array, 'name' ), the function will
     * return an array of values from $array[]->name
     *
     * @param   array   $array             An array
     * @param   string  $field             The field to get values from
     * @param   bool    $preserve_keys     Whether or not to preserve the
     *                                     array keys
     * @param   bool    $remove_nomatches  If the field doesn't appear to
     *                                     be set, remove it from the array
     * @return  array
     *
     * @link    http://codex.wordpress.org/Function_Reference/wp_list_pluck
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function array_pluck(array $array, $field, $preserve_keys = TRUE, $remove_nomatches = TRUE) {
        $new_list = array();

        foreach ($array as $key => $value) {
            if (is_object($value)) {
                if (isset($value->{$field})) {
                    if ($preserve_keys) {
                        $new_list[$key] = $value->{$field};
                    } else {
                        $new_list[] = $value->{$field};
                    }
                } else if (!$remove_nomatches) {
                    $new_list[$key] = $value;
                }
            } else {
                if (isset($value[$field])) {
                    if ($preserve_keys) {
                        $new_list[$key] = $value[$field];
                    } else {
                        $new_list[] = $value[$field];
                    }
                } else if (!$remove_nomatches) {
                    $new_list[$key] = $value;
                }
            }
        }

        return $new_list;
    }

    /**
     * Searches for a given value in an array of arrays, objects and scalar
     * values. You can optionally specify a field of the nested arrays and
     * objects to search in
     *
     * @param   array   $array   The array to search
     * @param   scalar  $search  The value to search for
     * @param   string  $field   The field to search in, if not specified
     *                           all fields will be searched
     * @return  bool|scalar      False on failure or the array key on
     *                           success
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function array_search_deep(array $array, $search, $field = FALSE) {
        // *grumbles* stupid PHP type system
        $search = (string) $search;

        foreach ($array as $key => $elem) {

            // *grumbles* stupid PHP type system
            $key = (string) $key;

            if ($field) {
                if (is_object($elem) && $elem->{$field} === $search) {
                    return $key;
                } else if (is_array($elem) && $elem[$field] === $search) {
                    return $key;
                } else if (is_scalar($elem) && $elem === $search) {
                    return $key;
                }
            } else {
                if (is_object($elem)) {
                    $elem = (array) $elem;

                    if (in_array($search, $elem)) {
                        return $key;
                    }
                } else if (is_array($elem) && in_array($search, $elem)) {
                    return array_search($search, $elem);
                } else if (is_scalar($elem) && $elem === $search) {
                    return $key;
                }
            }
        }

        return FALSE;
    }

    /**
     * Returns an array containing all the elements of arr1 after applying
     * the callback function to each one
     *
     * @param   string  $callback      Callback function to run for each
     *                                 element in each array
     * @param   array   $array         An array to run through the callback
     *                                 function
     * @param   bool    $on_nonscalar  Whether or not to call the callback
     *                                 function on nonscalar values
     *                                 (Objects, resources, etc)
     * @return  array
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function array_map_deep(array $array, $callback, $on_nonscalar = FALSE) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $args = array($value, $callback, $on_nonscalar);
                $array[$key] = call_user_func_array(array(__CLASS__, __FUNCTION__), $args);
            } else if (is_scalar($value) || $on_nonscalar) {
                $array[$key] = call_user_func($callback, $value);
            }
        }

        return $array;
    }

}