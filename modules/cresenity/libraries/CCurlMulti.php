<?php

/**
 * CCurlMulti
 */
//@codingStandardsIgnoreStart
class CCurlMulti {
    private $curl_multi_handle;

    private $curl;

    private $handle;

    private $last_response;

    private $last_response_body;

    private $last_response_header;

    private $active;

    private $curl_code;

    public function __construct() {
        $this->handle = [];
        $this->active = null;
    }

    public static function factory() {
        return new CCurlMulti();
    }

    public function clear_last_exec() {
        $this->last_response = null;
        $this->last_response_body = null;
        $this->last_response_header = null;
        return $this;
    }

    public function opened() {
        return $this->curl_multi_handle != null;
    }

    public function open() {
        $this->curl_multi_handle = curl_multi_init();
        return $this;
    }

    public function close() {
        @curl_multi_close($this->curl_multi_handle);
        return $this;
    }

    public function add_curl($name, $curl) {
        $this->curl[$name] = $curl;
        return $this;
    }

    /**
     * @param type       $url
     * @param type       $engine
     * @param null|mixed $name
     *
     * @return CCurl
     */
    public function add_handle($url, $engine = 'curl', $name = null) {
        $curl = CCurl::factory($url, $engine);
        if ($name != null) {
            $this->curl[$name] = $curl;
        } else {
            $this->curl[] = $curl;
        }
        return $curl;
    }

    public function last_response() {
        return $this->last_response;
    }

    public function exec() {
        // clear last exec
        $this->clear_last_exec();

        $this->open();

        foreach ($this->curl as $k => $v) {
            $handle = $v->exec(false)->get_handle();
            $this->handle[$k] = $handle;
            curl_multi_add_handle($this->curl_multi_handle, $handle);
        }

        do {
            $this->curl_code = curl_multi_exec($this->curl_multi_handle, $this->active);
            curl_multi_select($this->curl_multi_handle);
        } while ($this->active > 0);

        foreach ($this->handle as $k => $v) {
            $this->last_response[$k] = curl_multi_getcontent($v);
            curl_multi_remove_handle($this->curl_multi_handle, $v);
        }

        $this->close();

        return $this;
    }
}
