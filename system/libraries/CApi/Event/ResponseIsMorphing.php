<?php

class CApi_Event_ResponseIsMorphing {
    /**
     * Response instance.
     *
     * @var \CApi_HTTP_Response
     */
    public $response;

    /**
     * Response content.
     *
     * @var string
     */
    public $content;

    /**
     * Create a new response is morphing event. Content is passed by reference
     * so that multiple listeners can modify content.
     *
     * @param \CApi_HTTP_Response $response
     * @param string              $content
     *
     * @return void
     */
    public function __construct(CApi_HTTP_Response $response, &$content) {
        $this->response = $response;
        $this->content = &$content;
    }
}
