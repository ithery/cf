<?php


class CBase_StringParamable {
    /**
     * String
     *
     * @var string
     */
    protected $string;

    /**
     * Parameters
     *
     * @var array
     */
    protected $params;

    public function __construct($string, array $params = []) {
        $this->string = $string;
        if (!is_array($params)) {
            $params = [];
        }
        $this->params = $params;
    }

    public function setParams($params) {
        return $this->params = $params;
    }

    public function get() {
        $string = $this->string;
        preg_match_all("/{([\w]*)}/", $string, $matches, PREG_SET_ORDER);
        foreach ($matches as $val) {
            $str = $val[1]; //matches str without bracket {}
            $bStr = $val[0]; //matches str with bracket {}
            $value = carr::get($this->params, $str);
            if ($value !== null) {
                $string = str_replace($bStr, $value, $string);
            }
        }
        return $string;
    }

    public function getOriginal() {
        return $this->string;
    }

    public function __toString() {
        return $this->get();
    }
}
