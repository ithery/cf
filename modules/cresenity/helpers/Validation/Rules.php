<?php

    class Helpers_Validation_Rules {

        public static $prefix_code = 'DF';
        public static $err_code = 0;
        public static $err_message = '';
        public static $error_collection = array();
        public static $data = array();

        public static function phone($field, $data, $param = array()) {
            self::$err_code = 0;
            self::$prefix_code = 'PH';
            $value = carr::get($data, $field);
            switch ($param) {
                case "numeric":
                    return self::numeric($field, $data, $param);
                case "international":
                    return self::international($field, $data, $param);
                default:
                    self::numeric($field, $data, $param);
                    $err1 = self::$err_code;
                    self::international($field, $data, $param);
                    $err2 = self::$err_code;
                    self::$err_code = ($err1 == 0 || $err2 == 0) ? 0 : 1;
                    break;
            }
            return $value;
        }

        public static function international($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'AL';
            $value = carr::get($data, $field);
            if (!is_numeric($value)) {
                self::$err_code++;
                self::$err_message = "{$field} {$value} is non {$param}, please enter a valid number in this field.";
            }
            if (self::$err_code == 0 && substr($value, 0, 3) != "+62") {
                self::$err_code++;
                self::$err_message = "{$field} {$value} is not contain +62[phone_number], please use +62 in this field.";
            }
            if (self::$err_code == 0 && !substr($value, 3)) {
                self::$err_code++;
                self::$err_message = "{$field} {$value}, please fill phone number in this field.";
            }
            return $value;
        }

        public static function alpha($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'AL';
            $value = carr::get($data, $field);
            if (!cvalid::alpha($value)) {
                self::$err_code++;
                self::$err_message = "{$field} {$value} is not {$param}, please use letters only (a-z or A-Z) in this field.";
            }
            return $value;
        }

        public static function alpha_space($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'AL';
            $value = carr::get($data, $field);
            if (!(bool) preg_match('/^[a-z\-_\s]+$/i', $value)) {
                self::$err_code++;
                self::$err_message = "{$field} {$value} is not {$param}, please use letters only (a-z or A-Z) in this field.";
            }
            return $value;
        }

        public static function alpha_numeric($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'AN';
            $value = carr::get($data, $field);
            if (!cvalid::alpha_numeric($value)) {
                self::$err_code++;
                self::$err_message = "{$field} {$value} is not {$param}, please use only letters (a-z or A-Z) or numbers (0-9) only in this field. No spaces or other characters are allowed.";
            }
            return $value;
        }

        public static function numeric($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'NU';
            $value = carr::get($data, $field);
            if (!cvalid::numeric($value)) {
                self::$err_code++;
                self::$err_message = "{$field} {$value} is non {$param}, please enter a valid number in this field.";
            }
            return $value;
        }

        public static function max($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'MA';
            $numeric = doubleval(carr::get($data, $field));
            if ($numeric > $param) {
                self::$err_code++;
                self::$err_message = "{$field} {$numeric} is greater than {$param}.";
            }
            return $numeric;
        }

        public static function min($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'MI';
            $numeric = doubleval(carr::get($data, $field));
            if ($numeric < $param) {
                self::$err_code++;
                self::$err_message = "{$field} {$numeric} is less than {$param}.";
            }
            return $numeric;
        }

        public static function date_min($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'DI';
            $date = carr::get($data, $field);
            $date_min = date('Y-m-d', strtotime($param));
            if ($date < $date_min) {
                self::$err_code++;
                self::$err_message = "{$field} {$date} is less than {$param}.";
            }
            return $date;
        }

        public static function date_max($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'DA';
            $date = carr::get($data, $field);
            $date_max = date('Y-m-d', strtotime($param));
            if ($date > $date_max) {
                self::$err_code++;
                self::$err_message = "{$field} {$date} is greater than {$param}.";
            }
            return $date;
        }

        public static function possible_value($field, $data, $param = array()) {
            self::$err_code = 0;
            self::$prefix_code = 'PV';
            $datas = $data;
            $value = carr::get($data, $field);
            if (!in_array($value, $param)) {
                self::$err_code++;
                self::$err_message = "{$field} {$value} is not valid.";
            }
            return $value;
        }

        public static function required($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'RE';
            $value = carr::get($data, $field);            
            if (strlen($value) == 0) {
                self::$err_code++;
                self::$err_message = "{$field} is required.";
            }
            return $value;
        }

        public static function email($field, $data, $param = "") {
            self::$err_code = 0;
            self::$prefix_code = 'EM';
            $email = carr::get($data, $field);
            if (!cvalid::email($email)) {
                self::$err_code++;
                self::$err_message = "{$field} is not vaild.";
            }
            return $email;
        }

        public static function date_time($field, $data, $param = "", $format = 'Y-m-d H:i:s') {
            self::$err_code = 0;
            self::$prefix_code = 'DT';
            $date = carr::get($data, $field);
            $d = new DateTime(date($format, strtotime($date)));
            if (!($d && $d->format($format) == $date)) {
                self::$err_code++;
                self::$err_message = "{$field} format using({$format}) is not valid";
            }
            return $date;
        }

        public static function date($field, $data, $param = "") {
            $format = 'Y-m-d';
            return self::date_time($field, $data, $param, $format);
        }

        public static function time($field, $data, $param = "") {
            $format = 'H:i:s';
            return self::date_time($field, $data, $param, $format);
        }

        public static function custom($method, $param = "") {
            
        }

        public static function get_full_err_code() {
            return 'VR0002[' . self::$prefix_code . str_pad(self::$err_code, 4, '0', STR_PAD_LEFT) . ']';
        }

        public static function get(&$array, $key, $default = null) {
            foreach ($array as $key => $value) {
                $array = $key;
                return isset($value[$key]) ? $value[$key] : $default;
            }
        }

    }
    