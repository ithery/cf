<?php

class CApp_Visitor_Driver_JenssegersAgentDriver implements CApp_Visitor_Contract_UserAgentParserInterface {
    /**
     * Request container.
     *
     * @var CHTTP_Request
     */
    protected CHTTP_Request $request;

    /**
     * Agent parser.
     *
     * @var CApp_Visitor_Agent
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
     *
     * @return string
     */
    public function device() : string {
        return $this->parser->device();
    }

    /**
     * Retrieve platform's name.
     *
     * @return string
     */
    public function platform() : string {
        return $this->parser->platform();
    }

    /**
     * Retrieve browser's name.
     *
     * @return string
     */
    public function browser() : string {
        return $this->parser->browser();
    }

    /**
     * Retrieve languages.
     *
     * @return array
     */
    public function languages() : array {
        return $this->parser->languages();
    }

    /**
     * Initialize userAgent parser.
     *
     * @return CApp_Visitor_Agent
     */
    protected function initParser(): CApp_Visitor_Agent {
        $parser = new CApp_Visitor_Agent();
         // if($userAgent==null) {
        //     \cdbg::dd(\cdbg::getTraceString());
        // }

        $parser->setUserAgent($this->request->userAgent());
        $parser->setHttpHeaders((array) $this->request->headers);

        return $parser;
    }
}
