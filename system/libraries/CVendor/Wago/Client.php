<?php

class CVendor_Wago_Client {
    /**
     * @var CVendor_Wago_Contract_AdapterInterface
     */
    protected $adapter;

    protected $baseUri;

    public function __construct(CVendor_Wago_Contract_AdapterInterface $adapter, $baseUri) {
        $this->adapter = $adapter;

        $this->baseUri = $baseUri;
    }

    public function url($method, $queries = null) {
        $url = $this->baseUri . $method;
        $queryString = '';
        if ($queries != null) {
            if (is_string($queryString)) {
                $queryString = $queries;
            }
            if (is_array($queries)) {
                if (count($queries) > 0) {
                    $queryString = curl::asPostString($queryString);
                }
            }
            if (strlen($queryString) > 0) {
                $url .= '?' . $queryString;
            }
        }

        return $url;
    }

    public function get($method, $parameters = null, $headers = null) {
        $url = $this->url($method, is_string($parameters) ? $parameters : null);
        $response = $this->adapter->get($url, is_array($parameters) ? $parameters : null, $headers);
        $result = json_decode($response, true);

        return $result;
    }

    public function post($method, $parameters = null, $headers = null) {
        $url = $this->url($method);
        $response = $this->adapter->post($url, $parameters, $headers);

        return $response;
    }

    public function put($method, $parameters = null, $headers = null) {
        $url = $this->url($method);

        $response = $this->adapter->put($url, $parameters, $headers);

        return $response;
    }

    public function delete($method, $parameters = null, $headers = null) {
        $url = $this->url($method);
        $response = $this->adapter->delete($url, $parameters, $headers);
        $result = json_decode($response, true);

        return $result;
    }
}
