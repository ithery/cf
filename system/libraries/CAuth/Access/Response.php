<?php

use Illuminate\Contracts\Support\Arrayable;

class CAuth_Access_Response implements Arrayable {
    /**
     * Indicates whether the response was allowed.
     *
     * @var bool
     */
    protected $allowed;

    /**
     * The response message.
     *
     * @var null|string
     */
    protected $message;

    /**
     * The response code.
     *
     * @var mixed
     */
    protected $code;

    /**
     * Create a new response.
     *
     * @param bool   $allowed
     * @param string $message
     * @param mixed  $code
     *
     * @return void
     */
    public function __construct($allowed, $message = '', $code = null) {
        $this->code = $code;
        $this->allowed = $allowed;
        $this->message = $message;
    }

    /**
     * Create a new "allow" Response.
     *
     * @param null|string $message
     * @param mixed       $code
     *
     * @return CAuth_Access_Response
     */
    public static function allow($message = null, $code = null) {
        /** @phpstan-ignore-next-line */
        return new static(true, $message, $code);
    }

    /**
     * Create a new "deny" Response.
     *
     * @param null|string $message
     * @param mixed       $code
     *
     * @return CAuth_Access_Response
     */
    public static function deny($message = null, $code = null) {
        /** @phpstan-ignore-next-line */
        return new static(false, $message, $code);
    }

    /**
     * Determine if the response was allowed.
     *
     * @return bool
     */
    public function allowed() {
        return $this->allowed;
    }

    /**
     * Determine if the response was denied.
     *
     * @return bool
     */
    public function denied() {
        return !$this->allowed();
    }

    /**
     * Get the response message.
     *
     * @return null|string
     */
    public function message() {
        return $this->message;
    }

    /**
     * Get the response code / reason.
     *
     * @return mixed
     */
    public function code() {
        return $this->code;
    }

    /**
     * Throw authorization exception if response was denied.
     *
     * @throws CAuth_Exception_AuthorizationException
     *
     * @return CAuth_Access_Response
     */
    public function authorize() {
        if ($this->denied()) {
            throw (new CAuth_Exception_AuthorizationException($this->message(), $this->code()))
                ->setResponse($this);
        }

        return $this;
    }

    /**
     * Convert the response to an array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'allowed' => $this->allowed(),
            'message' => $this->message(),
            'code' => $this->code(),
        ];
    }

    /**
     * Get the string representation of the message.
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->message();
    }
}
