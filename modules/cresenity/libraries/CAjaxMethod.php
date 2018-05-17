<?php

class CAjaxMethod {

    use CTrait_Compat_AjaxMethod;
    
    public $name = "";
    public $method = "GET";
    public $data = array();
    public $type = "";
    public $target = "";
    public $param = array();

    public function __construct() {
        
    }

    public static function factory() {
        return new CAjaxMethod();
    }

    public function setData($key, $data) {
        $this->data[$key] = $data;
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    public function makeUrl($indent = 0) {
        $js = CStringBuilder::factory()->set_indent($indent);
        //generate ajax_method
        //save this object to file.
        $json = json_encode($this);

        $ajax_method = date('Ymd') . cutils::randmd5();
        $file = ctemp::makepath("ajax", $ajax_method . ".tmp");
        file_put_contents($file, $json);
        $base_url = curl::base();
        if (CApp::instance()->is_mobile()) {
            $base_url = curl::base(false, 'http');
        }
        return $base_url . "ccore/ajax/" . $ajax_method;
    }

}

?>