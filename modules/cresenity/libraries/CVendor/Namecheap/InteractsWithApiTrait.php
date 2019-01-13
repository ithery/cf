<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 25, 2018, 5:28:32 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Kregel\Namecheap\Api;
use Psr\Http\Message\StreamInterface;

/**
 * Trait InteractsWithApi
 * @mixin AbstractIntration
 */
trait CVendor_Namecheap_InteractsWithApiTrait {

    /**
     * The client variable use to make requests.
     *
     * @var Client
     */
    protected $_client;

    /**
     * Make the api request.
     *
     * @param $method
     * @param $url
     * @param array $params
     *
     * @return StreamInterface
     */
    private function sendRequest($method, $url, $params = []) {
        if ($this->_client == null) {
            throw new \DomainException('You must set the client');
        }
        $res = $this->_client->request($method, 'xml.response?Command=namecheap.' . $url . '&' . http_build_query(array_merge($this->config->toArray(), $params)), []);
        $body = $res->getBody();
        return $body;
    }

    public function request($method, $uri, array $params, callable $transformResponse) {
        $response = $this->sendRequest($method, $uri, $params);
        // Convert the request from XML to JSON
        $responseArray = json_decode(json_encode(simplexml_load_string($response->getContents())), true);
        $closureResult = $transformResponse($responseArray);
        if (!is_array($closureResult)) {
            throw new \DomainException(sprintf('Your closure for the following URI, is not returning an array [%s]', $uri));
        }
        return $closureResult;
    }

}
