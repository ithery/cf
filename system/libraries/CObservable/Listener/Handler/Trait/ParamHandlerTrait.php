<?php

trait CObservable_Listener_Handler_Trait_ParamHandlerTrait {
    protected $paramInputs;

    protected $paramInputsMultiple;

    protected $paramInputsByName;

    protected $paramRequest;

    protected function normalizeParamInput($inputs) {
        if (!is_array($inputs)) {
            $inputs = [$inputs];
        }
        $isAssoc = carr::isAssoc($inputs);

        return c::collect($inputs)->mapWithKeys(function ($input, $key) use ($isAssoc) {
            $selector = $input;
            if ($input instanceof CRenderable) {
                $selector = '#' . $input->id();
            }
            if (strlen($selector) > 0 && preg_match('/^[a-zA-Z0-9]/', $selector)) {
                $selector = '#' . $selector;
            }
            if (!$isAssoc) {
                $key = $input;
            }

            return [$key => $selector];
        })->toArray();
    }

    public function addParamInput($inputs) {
        $inputs = $this->normalizeParamInput($inputs);
        foreach ($inputs as $key => $inp) {
            $this->paramInputs[$key] = $inp;
        }

        return $this;
    }

    public function addParamInputMultiple($inputs) {
        $inputs = $this->normalizeParamInput($inputs);
        foreach ($inputs as $key => $inp) {
            $this->paramInputsMultiple[$key] = $inp;
        }

        return $this;
    }

    public function addParamRequest($paramRequest) {
        if (!is_array($paramRequest)) {
            $paramRequest = [$paramRequest];
        }
        foreach ($paramRequest as $reqK => $reqV) {
            $this->paramRequest[$reqK] = $reqV;
        }

        return $this;
    }

    public function addParamInputByName($inputs) {
        if (!is_array($inputs)) {
            $inputs = [$inputs];
        }
        foreach ($inputs as $k => $inp) {
            $this->paramInputsByName[$k] = $inp;
        }

        return $this;
    }

    public function populateParamJson() {
        $dataAddition = '';
        if (is_array($this->paramRequest)) {
            foreach ($this->paramRequest as $reqK => $reqV) {
                if (strlen($dataAddition) > 0) {
                    $dataAddition .= ',';
                }
                if (is_array($reqV)) {
                    if (carr::isAssoc($reqV)) {
                        $reqV = json_encode($reqV);
                    } else {
                        $reqV = implode(',', $reqV);
                    }
                }
                $dataAddition .= "'" . $reqK . "':'" . $reqV . "'";
            }
        }
        if (is_array($this->paramInputsMultiple)) {
            foreach ($this->paramInputsMultiple as $k => $inp) {
                if (strlen($dataAddition) > 0) {
                    $dataAddition .= ',';
                }
                $selector = $inp;
                if (strlen($selector) > 0 && preg_match('/^[a-zA-Z0-9]/', $selector)) {
                    $selector = '#' . $selector;
                }
                $dataAddition .= "'" . $k . "':cresenity.arrayValue('" . $selector . "')";
            }
        }
        if (is_array($this->paramInputs)) {
            foreach ($this->paramInputs as $k => $inp) {
                if (strlen($dataAddition) > 0) {
                    $dataAddition .= ',';
                }
                $selector = $inp;
                if (strlen($selector) > 0 && preg_match('/^[a-zA-Z0-9]/', $selector)) {
                    $selector = '#' . $selector;
                }
                $dataAddition .= "'" . $k . "':cresenity.value('" . $selector . "')";
            }
        }
        if (is_array($this->paramInputsByName)) {
            foreach ($this->paramInputsByName as $k => $inp) {
                if (strlen($dataAddition) > 0) {
                    $dataAddition .= ',';
                }
                $dataAddition .= "'" . $k . "':cresenity.value('" . $inp . "')";
            }
        }
        $dataAddition = '{' . $dataAddition . '}';

        return $dataAddition;
    }
}
