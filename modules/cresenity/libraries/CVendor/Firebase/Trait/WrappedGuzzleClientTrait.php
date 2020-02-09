<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 * @codeCoverageIgnore
 */
trait CVendor_Firebase_Trait_WrappedGuzzleClientTrait {

    /** @var ClientInterface */
    protected $client;

    public function send(RequestInterface $request, array $options = []) {
        return $this->client->send($request, $options);
    }

    public function sendAsync(RequestInterface $request, array $options = []) {
        return $this->client->sendAsync($request, $options);
    }

    public function request($method, $uri, array $options = []) {
        return $this->client->request($method, $uri, $options);
    }

    public function requestAsync($method, $uri, array $options = []) {
        return $this->client->requestAsync($method, $uri, $options);
    }

    public function getConfig($option = null) {
        return $this->client->getConfig($option);
    }

}
