<?php

use Dingo\Api\Http\Parser\Accept;

class CApi_HTTP_Request extends CHTTP_Request implements CApi_Contract_HTTP_RequestInterface {
    /**
     * Api Group.
     *
     * @var string
     */
    protected $apiGroup;

    /**
     * Parsed accept header for the request.
     *
     * @var array
     */
    protected $accept;

    /**
     * Create a new Dingo request instance from an Illuminate request instance.
     *
     * @param \CHTTP_Request $old
     *
     * @return \CApi_HTTP_Request
     */
    public function createFromBaseHttp(CHTTP_Request $old) {
        $new = new static(
            $old->query->all(),
            $old->request->all(),
            $old->attributes->all(),
            $old->cookies->all(),
            $old->files->all(),
            $old->server->all(),
            $old->content
        );

        if ($session = $old->getSession()) {
            $new->setSession($old->getSession());
        }

        $new->setRouteResolver($old->getRouteResolver());
        $new->setUserResolver($old->getUserResolver());

        return $new;
    }

    /**
     * @return CApi_Manager
     */
    protected function manager() {
        return CApi_Manager::instance($this->apiGroup);
    }

    /**
     * Get the defined version.
     *
     * @return string
     */
    public function version() {
        $this->parseAcceptHeader();

        return $this->accept['version'];
    }

    public function setApiGroup($apiGroup) {
        $this->apiGroup = $apiGroup;
    }

    /**
     * Get the defined subtype.
     *
     * @return string
     */
    public function subtype() {
        $this->parseAcceptHeader();

        return $this->accept['subtype'];
    }

    /**
     * Get the expected format type.
     *
     * @param mixed $default
     *
     * @return string
     */
    public function format($default = 'html') {
        $this->parseAcceptHeader();

        return $this->accept['format'] ?: parent::format($default);
    }

    /**
     * Parse the accept header.
     *
     * @return void
     */
    protected function parseAcceptHeader() {
        if ($this->accept) {
            return;
        }

        $this->accept = $this->manager()->httpParseAccept()->parse($this);
    }
}
