<?php

class CApi_HTTP_Request extends CHTTP_Request implements CApi_Contract_HTTP_RequestInterface {
    use CApi_Trait_HasGroupPropertyTrait;

    /**
     * Parsed accept header for the request.
     *
     * @var array
     */
    protected $accept;

    protected $apiData = [];

    protected $sessionResolver;

    /**
     * Create a new Dingo request instance from an Illuminate request instance.
     *
     * @param \CHTTP_Request $old
     *
     * @return \CApi_HTTP_Request
     */
    public static function createFromBaseHttp(CHTTP_Request $old) {
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
            $new->setSession($session);
        }

        $new->setRouteResolver($old->getRouteResolver());
        $new->setUserResolver($old->getUserResolver());

        return $new;
    }

    /**
     * Get the defined version.
     *
     * @return string
     */
    public function version() {
        return $this->accept['version'];
    }

    public function setGroup($group) {
        $this->group = $group;
        $this->parseAcceptHeader();
    }

    public function group() {
        return $this->group;
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

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setApiData($key, $value) {
        $this->apiData[$key] = $value;

        return $this;
    }

    /**
     * @param string     $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getApiData($key, $default = null) {
        return c::value(carr::get($this->apiData, $key, $default));
    }

    /**
     * Get the session associated with the request.
     *
     * @throws \RuntimeException
     *
     * @return \CSession_Store
     */
    public function session() {
        return call_user_func($this->getSessionResolver());
    }
    /**
     * Get the user resolver callback.
     *
     * @return \Closure
     */
    public function getSessionResolver() {
        return $this->sessionResolver ?: function () {
            return CBase::session();
        };
    }
    /**
     * Get the session associated with the request.
     *
     * @throws \RuntimeException
     *
     * @return \CSession_Store
     */
    public function setSessionResolver(Closure $callback) {
        $this->sessionResolver = $callback;

        return $this;
    }
}
