<?php

abstract class CApi_HTTP_Response_FormatAbstract {
    /**
     * Request instance.
     *
     * @var \CHTTP_Request
     */
    protected $request;

    /**
     * Response instance.
     *
     * @var \CHTTP_Response
     */
    protected $response;

    /*
     * Array of formats' options.
     *
     * @var array
     */
    protected $options;

    /**
     * Set the request instance.
     *
     * @param \CHTTP_Request $request
     *
     * @return \CApi_HTTP_Response_FormatAbstract
     */
    public function setRequest($request) {
        $this->request = $request;

        return $this;
    }

    /**
     * Set the response instance.
     *
     * @param \CHTTP_Response $response
     *
     * @return \CApi_HTTP_Response_FormatAbstract
     */
    public function setResponse($response) {
        $this->response = $response;

        return $this;
    }

    /**
     * Set the formats' options.
     *
     * @param array $options
     *
     * @return \CApi_HTTP_Response_FormatAbstract
     */
    public function setOptions(array $options) {
        $this->options = $options;

        return $this;
    }

    /**
     * Format an Eloquent model.
     *
     * @param \CModel $model
     *
     * @return string
     */
    abstract public function formatModel($model);

    /**
     * Format an Eloquent collection.
     *
     * @param \CModel_Collection $collection
     *
     * @return string
     */
    abstract public function formatModelCollection($collection);

    /**
     * Format an array or instance implementing Arrayable.
     *
     * @param array|\Illuminate\Contracts\Support\Arrayable $content
     *
     * @return string
     */
    abstract public function formatArray($content);

    /**
     * Get the response content type.
     *
     * @return string
     */
    abstract public function getContentType();
}
