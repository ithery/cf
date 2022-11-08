<?php

use ByJG\Util\Psr7\Response;
use ByJG\ApiTools\Base\Schema;
use ByJG\ApiTools\AbstractRequester;
use ByJG\Util\Psr7\MessageException;
use ByJG\ApiTools\Exception\NotMatchedException;
use ByJG\ApiTools\Exception\PathNotFoundException;
use ByJG\ApiTools\Exception\GenericSwaggerException;
use ByJG\ApiTools\Exception\InvalidDefinitionException;
use ByJG\ApiTools\Exception\DefinitionNotFoundException;
use ByJG\ApiTools\Exception\HttpMethodNotFoundException;
use ByJG\ApiTools\Exception\StatusCodeNotMatchedException;

abstract class CTesting_OpenApiTestCase extends CTesting_TestCase {
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var AbstractRequester
     */
    protected $requester = null;

    /**
     * Configure the schema to use for requests.
     *
     * When set, all requests without an own schema use this one instead.
     *
     * @param null|Schema|string $schema
     */
    public function setSchema($schema) {
        if (is_string($schema)) {
            if (CFile::isFile($schema)) {
                $schema = Schema::getInstance(file_get_contents($schema));
            } else {
                throw new Exception($schema . ' does not exists');
            }
        }

        $this->schema = $schema;
    }

    public function setRequester(AbstractRequester $requester) {
        $this->requester = $requester;
    }

    /**
     * @return AbstractRequester
     */
    protected function getRequester() {
        if (is_null($this->requester)) {
            $this->requester = new CTesting_OpenApi_ApiRequester();
        }

        return $this->requester;
    }

    /**
     * @param string     $method         The HTTP Method: GET, PUT, DELETE, POST, etc
     * @param string     $path           The REST path call
     * @param int        $statusExpected
     * @param null|array $query
     * @param null|array $requestBody
     * @param array      $requestHeader
     *
     * @throws DefinitionNotFoundException
     * @throws GenericSwaggerException
     * @throws HttpMethodNotFoundException
     * @throws InvalidDefinitionException
     * @throws NotMatchedException
     * @throws PathNotFoundException
     * @throws StatusCodeNotMatchedException
     * @throws MessageException
     *
     * @return mixed
     *
     * @deprecated Use assertRequest instead
     */
    protected function makeRequest(
        $method,
        $path,
        $statusExpected = 200,
        $query = null,
        $requestBody = null,
        $requestHeader = []
    ) {
        $this->checkSchema();
        $body = $this->requester
            ->withSchema($this->schema)
            ->withMethod($method)
            ->withPath($path)
            ->withQuery($query)
            ->withRequestBody($requestBody)
            ->withRequestHeader($requestHeader)
            ->assertResponseCode($statusExpected)
            ->send();

        // Note:
        // This code is only reached if the send is successful and
        // all matches are satisfied. Otherwise an error is throwed before
        // reach this
        $this->assertTrue(true);

        return $body;
    }

    /**
     * @param AbstractRequester $request
     *
     * @throws DefinitionNotFoundException
     * @throws GenericSwaggerException
     * @throws HttpMethodNotFoundException
     * @throws InvalidDefinitionException
     * @throws NotMatchedException
     * @throws PathNotFoundException
     * @throws StatusCodeNotMatchedException
     * @throws MessageException
     *
     * @return Response
     */
    public function assertRequest(AbstractRequester $request) {
        // Add own schema if nothing is passed.
        if (!$request->hasSchema()) {
            $this->checkSchema();
            $request = $request->withSchema($this->schema);
        }

        // Request based on the Swagger Request definitios
        $body = $request->send();

        // Note:
        // This code is only reached if the send is successful and
        // all matches are satisfied. Otherwise an error is throwed before
        // reach this
        $this->assertTrue(true);

        return $body;
    }

    /**
     * @throws GenericSwaggerException
     */
    protected function checkSchema() {
        if (!$this->schema) {
            throw new GenericSwaggerException('You have to configure a schema for either the request or the testcase');
        }
    }

    public function createRequester() {
        return new CTesting_OpenApi_ApiRequester();
    }
}
