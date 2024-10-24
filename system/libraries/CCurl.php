<?php

class CCurl {
    use CTrait_Compat_Curl;

    /**
     * CurlHandle.
     *
     * @var CurlHandle|mixed
     */
    private $handle;

    private $options;

    private $autoinit;

    private $last_followed;

    private $caseless;

    private $headers;

    private $last_status;

    private $last_response;

    private $last_response_body;

    private $last_response_header;

    private $url;

    private $engine;

    private $soap_action;

    private $http_user_agent;

    private $post_data;

    private $last_caseless;

    private $opened;

    private $last_headers;

    private function __construct($url = null, $engine = 'curl') {
        $this->autoinit = true;
        $this->opened = false;
        $this->handle = null;
        $this->options = [];
        $this->last_status = [];
        $this->last_followed = [];
        $this->last_caseless = [];
        $this->last_headers = [];
        $this->last_response = null;
        $this->last_response_body = null;
        $this->last_response_header = null;
        $this->url = $url;
        $this->engine = $engine;
        $this->http_user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64)';
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->http_user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
    }

    private function clearLastExec() {
        $this->last_status = [];
        $this->last_followed = [];
        $this->last_caseless = [];
        $this->last_headers = [];
        $this->last_response = null;
        $this->last_response_body = null;
        $this->last_response_header = null;
    }

    public static function factory($url, $engine = 'curl') {
        return new CCurl($url, $engine);
    }

    public function opened() {
        return $this->handle != null;
    }

    public function __destruct() {
        if ($this->opened()) {
            $this->close();
        }
    }

    public function setSoapAction($action) {
        $this->soap_action = $action;

        return $this;
    }

    public function getInfo($opt = 0) {
        return curl_getinfo($this->handle, $opt);
    }

    public function setEngine($engine) {
        $this->engine = $engine;

        return $this;
    }

    public function open() {
        switch ($this->engine) {
            case 'curl':
                $this->handle = curl_init();

                break;
            case 'soapclient':
                $http_header = [
                    'trace' => true,
                    'location' => $this->url,
                    'uri' => $this->url,
                ];
                $this->handle = new SoapClient(null, $http_header);

                break;
        }

        return $this;
    }

    public function close() {
        switch ($this->engine) {
            case 'curl':
                @curl_close($this->handle);

                break;
        }
        $this->handle = null;

        return $this;
    }

    public function setOpt($key, $value, $overwrite = true) {
        if (!$overwrite) {
            if (isset($this->options[$key])) {
                return $this;
            }
        }
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Get value from key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getOpt($key) {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        return null;
    }

    public function getHandle() {
        return $this->handle;
    }

    public function setHttpUserAgent($http_user_agent) {
        $this->http_user_agent = $http_user_agent;

        return $this;
    }

    public function exec($exec = true) {
        //clear last exec
        $this->clearLastExec();

        //open curl connection
        if (!$this->opened()) {
            $this->open();
        }
        //set default options

        if ($this->engine == 'soapclient') {
            $request = $this->getOpt(CURLOPT_POSTFIELDS);
            if ($this->handle != null) {
                $this->last_response_body = $this->last_response = $this->handle->__doRequest($request, $this->url, $this->soap_action, 1);

                return true;
            }

            return false;
        }

        curl_setopt($this->handle, CURLOPT_TIMEOUT, 3000);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handle, CURLOPT_URL, $this->url);
        curl_setopt($this->handle, CURLOPT_USERAGENT, $this->http_user_agent);
        curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->handle, CURLOPT_REFERER, $this->url);
        curl_setopt($this->handle, CURLOPT_HEADER, false);
        //curl_setopt($this->handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        //set all option
        foreach ($this->options as $k => $v) {
            curl_setopt($this->handle, $k, $v);
        }

        if ($exec == true) {
            //exec curl
            $result = curl_exec($this->handle);
            $this->last_response = $result;
            $this->last_response_header = null;
            $this->last_response_body = $result;
            //get status
            $this->last_status = curl_getinfo($this->handle);
            $this->last_status['errno'] = curl_errno($this->handle);
            $this->last_status['error'] = curl_error($this->handle);

            // If there has been a curl error, just return this object.
            if ($this->last_status['errno']) {
                return $this;
            }

            //no error, we try parse the header
            if ($this->getOpt(CURLOPT_HEADER)) {
                $this->last_followed = [];
                $rv = $result;

                while (count($this->last_followed) <= $this->last_status['redirect_count']) {
                    $arr = preg_split("/(\r\n){2,2}/", $rv, 2);
                    $this->last_followed[] = $arr[0];
                    $rv = $arr[1];
                }
                $this->last_response_header = $arr[0];
                $this->parseHeader($this->last_response_header);

                $this->last_response_body = $arr[1];
            }
        }

        return $this;
    }

    /**
     * Parse Header.
     *
     * @param string $header_str
     *
     * @return void
     */
    public function parseHeader($header_str = null) {
        if ($header_str == null) {
            $header_str = $this->last_response_header;
        }
        $this->last_caseless = [];

        $arr = preg_split("/(\r\n)+/", $header_str);

        //
        // Ditch the HTTP status line.
        //

        if (preg_match('/^HTTP/', $arr[0])) {
            $arr = array_slice($arr, 1);
        }

        foreach ($arr as $hstr) {
            $hstr_arr = preg_split("/\s*:\s*/", $hstr, 2);

            $caseless_tag = strtoupper($hstr_arr[0]);

            if (!isset($this->last_caseless[$caseless_tag])) {
                $this->last_caseless[$caseless_tag] = $hstr_arr[0];
            }
            if (!isset($this->last_headers[$this->last_caseless[$caseless_tag]])) {
                $this->last_headers[$this->last_caseless[$caseless_tag]] = [];
            }
            $this->last_headers[$this->last_caseless[$caseless_tag]][] = $hstr_arr[1];
        }
    }

    public function getStatus($key = null) {
        if (empty($this->last_status)) {
            return false;
        }
        if (empty($key)) {
            return $this->last_status;
        } else {
            if (isset($this->last_status[$key])) {
                return $this->last_status[$key];
            }
        }
    }

    public function getHttpCode() {
        $httpcode = 0;
        switch ($this->engine) {
            case 'curl':
                $httpcode = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

                break;
            case 'soapclient':
                $httpcode = '200';

                break;
        }

        return $httpcode;
    }

    public function getHeader($caseless = null) {
        if (empty($this->last_headers)) {
            return false;
        }

        if (empty($caseless)) {
            return $this->last_headers;
        } else {
            $caseless = strtoupper($caseless);
            if (isset($this->last_caseless[$caseless])) {
                return $this->last_headers[$this->last_caseless[$caseless]];
            } else {
                return false;
            }
        }
    }

    public function getFollowedHeaders() {
        $arr = [];
        if ($this->last_followed) {
            foreach ($this->last_followed as $ah) {
                $arr[] = explode("\r\n", $ah);
            }

            return $arr;
        }

        return $arr;
    }

    public function hasError() {
        if (isset($this->last_status['error'])) {
            return empty($this->last_status['error']) ? false : $this->last_status['error'];
        } else {
            return false;
        }
    }

    /**
     * Get last response.
     *
     * @return string
     */
    public function response() {
        return $this->last_response_body;
    }

    public function setCookiesFile($filename) {
        $this->setOpt(CURLOPT_COOKIEJAR, $filename);
        $this->setOpt(CURLOPT_COOKIEFILE, $filename);

        return $this;
    }

    public function setTimeout($milisecond) {
        $this->setOpt(CURLOPT_TIMEOUT, $milisecond);

        return $this;
    }

    public function setRawPost($string) {
        $this->post_data = $string;
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $string);

        return $this;
    }

    public function setPost(array $data) {
        $this->post_data = $data;
        $post_data = curl::asPostString($data);
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $post_data);

        return $this;
    }

    public function setSSL() {
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, false);

        return $this;
    }

    public function setReferrer($referrer) {
        $this->setOpt(CURLOPT_REFERER, $referrer);

        return $this;
    }

    public function setUserAgent($useragent) {
        $this->setOpt(CURLOPT_USERAGENT, $useragent);

        return $this;
    }

    public function setHttpHeader($http_header) {
        $this->setOpt(CURLOPT_HTTPHEADER, $http_header);

        return $this;
    }

    public function setUrl($url) {
        $this->setOpt(CURLOPT_URL, $url);
        $this->url = $url;

        return $this;
    }

    public function getPostData() {
        return $this->post_data;
    }

    public static function builder() {
        return new CCurl_Builder();
    }
}
