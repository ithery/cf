<?php

/**
 * @mixin \CEmail_Message
 */
class CEmail_TextMessage {
    use CTrait_ForwardsCalls;

    /**
     * The underlying message instance.
     *
     * @var \CEmail_Message
     */
    protected $message;

    /**
     * Create a new text message instance.
     *
     * @param \CEmail_Message $message
     *
     * @return void
     */
    public function __construct($message) {
        $this->message = $message;
    }

    /**
     * Embed a file in the message and get the CID.
     *
     * @param string|\CEmail_Contract_AttachableInterface|\CEMail_Attachment $file
     *
     * @return string
     */
    public function embed($file) {
        return '';
    }

    /**
     * Embed in-memory data in the message and get the CID.
     *
     * @param string|resource $data
     * @param string          $name
     * @param null|string     $contentType
     *
     * @return string
     */
    public function embedData($data, $name, $contentType = null) {
        return '';
    }

    /**
     * Dynamically pass missing methods to the underlying message instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->forwardDecoratedCallTo($this->message, $method, $parameters);
    }
}
