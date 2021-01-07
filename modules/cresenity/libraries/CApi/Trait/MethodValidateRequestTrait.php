<?php
trait CApi_Trait_MethodValidateRequestTrait {
    public function validateRequestNotEmpty($key, $message = null) {
        $val = null;
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);
            if (strlen($val) == 0) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang('Parameter :key is empty', [':key' => $key]);
            }
        }
        return $val;
    }

    public function validateRequestMaxLength($key, $length, $message = null) {
        $val = null;
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);
            $valLength = strlen($val);
            if ($valLength > $length) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang('Parameter :key max length is :length', [':key' => $key, ':length' => $length]);
            }
        }
        return $val;
    }

    public function validateRequestInArray($key, $array, $message = null) {
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);
            if (!in_array($val, $array)) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang('Parameter :key is must in this possible values: :description', [':key' => $key, ':description' => implode(',', $array)]);
            }
        }
    }

    public function validateRequestIsArray($key, $message = null) {
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);

            if (!is_array($val)) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang('Parameter :key must be array', [':key' => $key]);
            }
        }
    }

    public function validateRequestIsDatetime($key, $message = null) {
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);

            if (!TBUtils::validDateTime($val)) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang('Parameter :key value must be format Y-m-d H:i:s', [':key' => $key]);
            }
        }
    }

    public function validateRequestIsNumeric($key, $message = null) {
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);
            if (!is_numeric($val)) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang('tbcore.parameterKeyValueMustBeNumeric', [':key' => $key]);
            }
        }
    }

    public function validateRequestIsUrl($key, $message = null) {
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);
            if (!filter_var($val, FILTER_VALIDATE_URL)) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang('Key :key is not valid url', [':key' => $key]);
            }
        }
    }

    public function validateRequestIsEmail($key, $message = null) {
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);
            if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang('Key :key is not valid email', [':key' => $key]);
            }
        }
    }

    public function validateRequestIsNotUrl($key, $message = null) {
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);
            //if (filter_var($val, FILTER_VALIDATE_URL)) {
            //    $this->errCode++;
            //    $this->errMessage = $this->lang($key . ' is url');
            //}
            $regex = '/(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\-\.@:%_\+~#=]+)+((\.[a-zA-Z])*)(\/([0-9A-Za-z-\-\.@:%_\+~#=\?])*)*/';
            $match = [];
            preg_match($regex, $val, $match);
            if (count($match) > 0) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang($key . ' is url');
            }
        }
    }

    public function validateDateValidYmd($key, $message = null) {
        if ($this->errCode == 0) {
            $val = carr::get($this->request(), $key);
            $tempVal = explode('-', $val);
            $year = carr::get($tempVal, 0);
            $month = carr::get($tempVal, 1);
            $date = carr::get($tempVal, 2);
            if (!$year || !$month || !$date) {
                $this->errCode++;
                $this->errMessage = $message ?: $this->lang($key . ' invalid date format');
            }
        }
    }
}
