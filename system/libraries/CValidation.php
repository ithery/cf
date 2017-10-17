<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * CValidation library.
 *
 */
class CValidation implements ArrayAccess {

    /**
     * Creates a new CValidation instance.
     *
     * @param   array   $array  array to use for validation
     * @return  Validation
     */
    public static function factory(array $array) {
        return new CValidation($array);
    }

    // Field rules
    protected $_rules = array();
    // Rules that are executed even when the value is empty
    protected $_empty_rules = array('not_empty', 'matches');
    // Error list, field => rule
    protected $_errors = array();
    // Array to validate
    protected $_data = array();
    // Bound values
    protected $_bound = array();

    /**
     * Sets the unique "any field" key and creates an ArrayObject from the
     * passed array.
     *
     * @param   array   $array  array to validate
     * @return  void
     */
    public function __construct(array $array) {
        $this->_data = $array;
    }

    /**
     * Throws an exception because Validation is read-only.
     * Implements ArrayAccess method.
     *
     * @throws  Kohana_Exception
     * @param   string   $offset    key to set
     * @param   mixed    $value     value to set
     * @return  void
     */
    public function offsetSet($offset, $value) {
        throw new CF_Exception('Validation objects are read-only.');
    }

    /**
     * Checks if key is set in array data.
     * Implements ArrayAccess method.
     *
     * @param   string  $offset key to check
     * @return  bool    whether the key is set
     */
    public function offsetExists($offset) {
        return isset($this->_data[$offset]);
    }

    /**
     * Throws an exception because Validation is read-only.
     * Implements ArrayAccess method.
     *
     * @throws  Kohana_Exception
     * @param   string  $offset key to unset
     * @return  void
     */
    public function offsetUnset($offset) {
        throw new CF_Exception('Validation objects are read-only.');
    }

    /**
     * Gets a value from the array data.
     * Implements ArrayAccess method.
     *
     * @param   string  $offset key to return
     * @return  mixed   value from array
     */
    public function offsetGet($offset) {
        return $this->_data[$offset];
    }

    public function rule($field, $rule, array $params = null, $error_message) {
        if ($params === NULL) {
            // Default to array(':value')
            $params = array(':value');
        }
        // Store the rule and params for this rule
        $this->_rules[$field][] = array($rule, $params, $error_message);

        return $this;
    }

    /**
     * Add rules using an array.
     *
     * @param   string  $field  field name
     * @param   array   $rules  list of callbacks
     * @return  $this
     */
    public function rules($field, array $rules) {
        foreach ($rules as $rule) {
            $this->rule($field, $rule[0], carr::get($rule, 1));
        }

        return $this;
    }

    /**
     * Bind a value to a parameter definition.
     *
     *     // This allows you to use :model in the parameter definition of rules
     *     $validation->bind(':model', $model)
     *         ->rule('status', 'valid_status', array(':model'));
     *
     * @param   string  $key    variable name or an array of variables
     * @param   mixed   $value  value
     * @return  $this
     */
    public function bind($key, $value = NULL) {
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $this->_bound[$name] = $value;
            }
        } else {
            $this->_bound[$key] = $value;
        }

        return $this;
    }

    public function check() {
        // New data set
        $data = $this->_errors = array();

        // Store the original data because this class should not modify it post-validation
        $original = $this->_data;

        // Get a list of the expected fields
        $expected = array_keys($original);

        // Import the rules locally
        $rules = $this->_rules;

        foreach ($expected as $field) {
            // Use the submitted value or NULL if no data exists
            $data[$field] = carr::get($this, $field);

            if (isset($rules[TRUE])) {
                if (!isset($rules[$field])) {
                    // Initialize the rules for this field
                    $rules[$field] = array();
                }

                // Append the rules
                $rules[$field] = array_merge($rules[$field], $rules[TRUE]);
            }
        }

        // Overload the current array with the new one
        $this->_data = $data;

        // Remove the rules that apply to every field
        unset($rules[TRUE]);

        // Bind the validation object to :validation
        $this->bind(':validation', $this);
        // Bind the data to :data
        $this->bind(':data', $this->_data);

        // Execute the rules
        foreach ($rules as $field => $set) {
            // Get the field value
            $value = carr::path($this->_data,$field);

            // Bind the field name and value to :field and :value respectively
            $this->bind(array
                (
                ':field' => $field,
                ':value' => $value,
            ));

            foreach ($set as $array) {
                // Rules are defined as array($rule, $params)
                list($rule, $params, $error_message) = $array;

                foreach ($params as $key => $param) {
                    if (is_string($param) AND array_key_exists($param, $this->_bound)) {
                        // Replace with bound value
                        $params[$key] = $this->_bound[$param];
                    }
                }
                
                if(is_callable($rule)) {
                    $passed = call_user_func_array($rule, $params);
                } elseif (method_exists('CValidator', $rule)) {
                    // Use a method in this object
                    $method = new ReflectionMethod('CValidator', $rule);

                    // Call static::$rule($this[$field], $param, ...) with Reflection
                    $passed = $method->invokeArgs(NULL, $params);
                } elseif (strpos($rule, '::') === FALSE) {
                    // Use a function call
                    $function = new ReflectionFunction($rule);

                    // Call $function($this[$field], $param, ...) with Reflection
                    $passed = $function->invokeArgs($params);
                } else {
                    // Split the class and method of the rule
                    list($class, $method) = explode('::', $rule, 2);

                    // Use a static method call
                    $method = new ReflectionMethod($class, $method);

                    // Call $Class::$method($this[$field], $param, ...) with Reflection
                    $passed = $method->invokeArgs(NULL, $params);
                }

                // Ignore return values from rules when the field is empty
                if (!in_array($rule, $this->_empty_rules) AND ! CValidator::not_empty($value))
                    continue;

                if ($passed === FALSE AND $error_message !== FALSE) {
                    // Add the error_message to the errors 
                    
                    $this->_errors[$field] = $error_message;

                    // This field has an error, stop executing rules
                    break;
                } elseif (isset($this->_errors[$field])) {
                    // The callback added the error manually, stop checking rules
                    break;
                }
            }
        }

        // Restore the data to its original form
        $this->_data = $original;


        return empty($this->_errors);
    }

    /**
     * Returns the error messages.
     * @return  array
     */
    public function errors() {

        // Return the error list
        return $this->_errors;
    }

    public function first_error() {
        return array_shift($this->_errors);
    }

}

// End CValidation
