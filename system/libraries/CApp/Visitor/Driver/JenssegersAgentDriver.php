<?php

class CApp_Visitor_Driver_JenssegersAgentDriver implements CApp_Visitor_Contract_UserAgentParserInterface {
    /**
     * Request container.
     */
    protected CHTTP_Request $request;

    /**
     * Agent parser.
     */
    protected CApp_Visitor_Agent $parser;

    /**
     * Parser constructor.
     *
     * @param CHTTP_Request $request
     */
    public function __construct(CHTTP_Request $request) {
        $this->request = $request;
        $this->parser = $this->initParser();
    }

    /**
     * Retrieve device's name.
     */
    public function device() : string {
        return $this->parser->device();
    }

    /**
     * Retrieve platform's name.
     */
    public function platform() : string {
        return $this->parser->platform();
    }

    /**
     * Retrieve browser's name.
     */
    public function browser() : string {
        return $this->parser->browser();
    }

    /**
     * Retrieve languages.
     */
    public function languages() : array {
        return $this->parser->languages();
    }

    /**
     * Initialize userAgent parser.
     */
    protected function initParser(): CApp_Visitor_Agent {
        $parser = new CApp_Visitor_Agent();

        $parser->setUserAgent($this->request->userAgent());
        $parser->setHttpHeaders((array) $this->request->headers);

        return $parser;
    }
}
