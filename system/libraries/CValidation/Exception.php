<?php

defined('SYSPATH') or die('No direct script access.');

class CValidation_Exception extends Exception {
    /**
     * The validator instance.
     *
     * @var CValidation_Validator
     */
    public $validator;

    /**
     * The recommended response to send to the client.
     *
     * @var \Symfony\Component\HttpFoundation\Response|null
     */
    public $response;

    /**
     * The status code to use for the response.
     *
     * @var int
     */
    public $status = 422;

    /**
     * The name of the error bag.
     *
     * @var string
     */
    public $errorBag;

    /**
     * The path the client should be redirected to.
     *
     * @var string
     */
    public $redirectTo;

    /**
     * Create a new exception instance.
     *
     * @param CValidation_Validator                      $validator
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string                                     $errorBag
     *
     * @return void
     */
    public function __construct($validator, $response = null, $errorBag = 'default') {
        parent::__construct('The given data was invalid. ' . $validator->getAllErrorString());

        $this->response = $response;
        $this->errorBag = $errorBag;
        $this->validator = $validator;
    }

    /**
     * Create a new validation exception from a plain array of messages.
     *
     * @param array $messages
     *
     * @return static
     */
    public static function withMessages(array $messages) {
        return new static(c::tap(CValidation::factory()->make([], []), function ($validator) use ($messages) {
            foreach ($messages as $key => $value) {
                foreach (carr::wrap($value) as $message) {
                    $validator->errors()->add($key, $message);
                }
            }
        }));
    }

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function errors() {
        return $this->validator->errors()->messages();
    }

    /**
     * Set the HTTP status code to be used for the response.
     *
     * @param int $status
     *
     * @return $this
     */
    public function status($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Set the error bag on the exception.
     *
     * @param string $errorBag
     *
     * @return $this
     */
    public function errorBag($errorBag) {
        $this->errorBag = $errorBag;

        return $this;
    }

    /**
     * Set the URL to redirect to on a validation error.
     *
     * @param string $url
     *
     * @return $this
     */
    public function redirectTo($url) {
        $this->redirectTo = $url;

        return $this;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function getResponse() {
        return $this->response;
    }
}
