<?php

    class Helpers_Validation_Api {

        /**
         * 
         * @param type $fields
         * @param type $data
         * @return type
         * @throws Helpers_Api_Validation_Exception
         */
        public static function validate($fields, $data, $version = 1) {
            $err_code = 0;
            $err_message = '';

            $fields = carr::get($fields, 'input', $fields);

            $str_func = 'validation_ver_' . $version;
            if (method_exists('Helpers_Validation_Api', $str_func)) {
                Helpers_Validation_Api::$str_func($fields, $data);
            }
            else {
                $err_code++;
                $err_message = 'Method ' . $str_func . ' doesnt exists';
                throw new Helpers_Validation_Api_Exception($err_message, $err_code);
            }

            if ($err_code == 0) {
                $collection = Helpers_Validation_Rules::$error_collection;
                if (count($collection) > 0) {
                    foreach ($collection as $field) {
                        foreach ($field as $rule) {
                            foreach ($rule as $err_code => $err_message) {
                                throw new Helpers_Validation_Api_Exception($err_message, $err_code, Helpers_Validation_Rules::$data);
                            }
                        }
                    }
                }
            }
            return $data;
        }

        private static function validation_ver_1($fields, $data) {
            foreach ($fields as $k_field => $rules) {
                if (is_array($rules)) {
                    Helpers_Validation_Api::check_rules($k_field, $rules, $data);
                }
                else {
                    //error
                    Helpers_Validation_Rules::$error_collection[$k_field][$rules] = array(
                        'VR0001' => $k_field . ' is not array');
                }
            }
        }

        private static function validation_ver_2($fields, $data) {
            foreach ($fields as $fields_k => $fields_v) {
                if (is_array($fields_v)) {
                    $data_type = carr::get($fields_v, 'data_type', '');
                    $rules = carr::get($fields_v, 'rules', array());
                    if ($data_type == 'array') {
                        $rev_fields = carr::get($fields_v, 'field', array());
                        $rev_data = carr::get($data, $fields_k, array());
                        Helpers_Validation_Api::validation_ver_2($rev_fields, $rev_data);
                    }
                    else if ($data_type == 'array2D') {
                        $rev_data = carr::get($data, $fields_k, array());
                      
                        foreach ($rev_data as $k => $v) {
                            $rev_fields = carr::get($fields_v, 'field', array());
//                            $rev_data = carr::get($rev_data, $k, array());
                            Helpers_Validation_Api::validation_ver_2($rev_fields, $v);
                        }
                    }
                    else {
                        Helpers_Validation_Api::check_rules($fields_k, $rules, $data);
                    }
                }
                else {
                    //Error
                    Helpers_Validation_Rules::$error_collection[$fields_k][$fields_v] = array(
                        'VR0001' => $fields_k . ' is not array');
                }
            }
        }

        private static function check_rules($fields_k, $rules, $data) {
            foreach ($rules as $rules_k => $rules_v) {
                $curr_rules = array();
                if (!is_array($rules_v)) {
                    $curr_rules[$rules_v] = $rules_v;
                }
                else {
                    $curr_rules = $rules_v;
                }
                foreach ($curr_rules as $rule_k => $rule) {
                    if (method_exists('Helpers_Validation_Rules', $rule_k)) {
                        Helpers_Validation_Rules::$rule_k($fields_k, $data, $rule);
                        if (Helpers_Validation_Rules::$err_code > 0) {
                            Helpers_Validation_Rules::$error_collection[$fields_k][$rule_k] = array(
                                Helpers_Validation_Rules::get_full_err_code() => Helpers_Validation_Rules::$err_message);
                        }
                    }
                    else {
                        Helpers_Validation_Rules::$error_collection[$fields_k][$rule_k] = array(
                            'VR0001' => $rule_k . ' doesnt exists');
                    }
                }
            }
        }

    }
    