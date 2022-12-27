<?php

class CVendor_Watzap_Client {
    /**
     * @var CVendor_Watzap_Contract_AdapterInterface
     */
    protected $adapter;

    protected $baseUri;

    public function __construct(CVendor_Watzap_Contract_AdapterInterface $adapter, $baseUri) {
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

    /**
     * @param string     $method
     * @param null|array $parameters
     * @param null|array $headers
     *
     * @return null|array
     */
    public function post($method, $parameters = null, $headers = null) {
        $url = $this->url($method);
        $response = $this->adapter->post($url, $parameters, $headers);
        $result = json_decode($response, true);

        return $result;
    }
}
