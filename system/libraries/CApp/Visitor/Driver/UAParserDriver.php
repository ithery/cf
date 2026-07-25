<?php
use UAParser\Parser;

class CApp_Visitor_Driver_UAParserDriver implements CApp_Visitor_Contract_UserAgentParserInterface {
    /**
     * Request container.
     *
     * @var CHTTP_Request
     */
    protected CHTTP_Request $request;

    /**
     * Agent parser.
     *
     * @var \UAParser\Result\Client
     */
    protected \UAParser\Result\Client $parser;

    /**
     * UAParser constructor.
     *
     * @param CHTTP_Request $request
     *
     * @throws \UAParser\Exception\FileNotFoundException
     */
    public function __construct(CHTTP_Request $request) {
        $this->request = $request;
        $this->parser = $this->initParser();
    }

    /**
     * Retrieve device's name.
     *
     * @return string
     */
    public function device() : string {
        return $this->parser->device->family;
    }

    /**
     * Retrieve platform's name.
     *
     * @return string
     */
    public function platform() : string {
        return $this->parser->os->family;
    }

    /**
     * Retrieve browser's name.
     *
     * @return string
     */
    public function browser() : string {
        return $this->parser->ua->family;
    }

    /**
     * Retrieve languages.
     *
     * @return array
     */
    public function languages() : array {
        $languages = [];

        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $languages[] = $lang;
        }

        return $languages;
    }

    /**
     * Initialize userAgent parser.
     *
     * @throws \UAParser\Exception\FileNotFoundException
     *
     * @return \UAParser\Result\Client
     */
    protected function initParser(): UAParser\Result\Client {
        return Parser::create()->parse($this->request->userAgent());
    }
}
