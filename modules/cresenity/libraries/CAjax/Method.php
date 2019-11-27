<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 9:59:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Method implements CInterface_Jsonable {

    public $name = "";
    public $method = "GET";
    public $data = array();
    public $type = "";
    public $target = "";
    public $param = array();

    public function __construct($options = array()) {
        if ($options == null) {
            $options = array();
        }
        $this->fromArray($options);
    }

    /**
     * 
     * @param type $key
     * @param type $data
     * @return $this
     */
    public function setData($key, $data) {
        $this->data[$key] = $data;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getData() {
        return $this->data;
    }

    /**
     * 
     * @param type $type
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * 
     * @param type $method
     * @return $this
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * 
     * @param type $jsonOption
     * @return type
     */
    public function makeUrl($jsonOption = 0) {
        //generate ajax_method
        $json = $this->toJson($jsonOption);

        //save this object to file.
        $ajaxMethod = date('Ymd') . cutils::randmd5();
        $file = CApp::temp()->makePath("ajax", $ajaxMethod . ".tmp");
        file_put_contents($file, $json);
        $base_url = curl::httpbase();

        return $base_url . "cresenity/ajax/" . $ajaxMethod;
    }

    /**
     * 
     * @param int $options
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this, $options);
    }

    /**
     * 
     * @param string $json
     * @return $this
     */
    public function fromJson($json) {
        $jsonArray = json_decode($json, true);
        return $this->fromArray($jsonArray);
    }

    public function fromArray(array $array) {
        $this->data = carr::get($array, 'data', array());
        $this->method = carr::get($array, 'method', 'GET');
        $this->type = carr::get($array, 'type');
        return $this;
    }

    /**
     * 
     * @param string $json
     * @return CAjax_Method
     */
    public static function createFromJson($json) {
        $instance = new CAjax_Method();
        return $instance->fromJson($json);
    }

    /**
     * 
     * @param CAjax_Method $ajaxMethod
     * @param array|null $input
     * @return CAjax_Engine
     * @throws CAjax_Exception
     */
    public static function createEngine(CAjax_Method $ajaxMethod, $input = null) {
        $class = 'CAjax_Engine_' . $ajaxMethod->type;
        
        if (!class_exists($class)) {
            throw new CAjax_Exception('class ajax engine :class not found', array(':class' => $class));
        }
        $engine = new $class($ajaxMethod, $input);
        return $engine;
    }

    /**
     * 
     * @param type $input
     * @return type
     */
    public function executeEngine($input = null) {
        
        $engine = self::createEngine($this, $input);
        return $engine->execute();
    }

}
